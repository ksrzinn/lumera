<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientListController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $patients = Conversation::where('professional_id', $request->user()->id)
            ->with('patient:id,name,email')
            ->latest('updated_at')
            ->get()
            ->groupBy('patient_id')
            ->map(fn ($conversations) => [
                'id' => $conversations->first()->patient->id,
                'name' => $conversations->first()->patient->name,
                'email' => $conversations->first()->patient->email,
                'conversations' => $conversations->count(),
                'open_conversation_id' => $conversations->firstWhere('status', Conversation::STATUS_OPEN)?->id,
                'last_conversation_id' => $conversations->first()->id,
            ])
            ->values();

        return Inertia::render('Professional/Patients', ['patients' => $patients]);
    }
}
