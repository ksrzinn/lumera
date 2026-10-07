<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfessionalDashboardController extends Controller
{
    private const LIST_LIMIT = 5;

    public function __invoke(Request $request): Response
    {
        $conversations = Conversation::where('professional_id', $request->user()->id)
            ->with(['patient:id,name', 'latestMessage'])
            ->latest('updated_at')
            ->get();

        $pending = $conversations->filter->awaitsProfessionalReply();

        $present = fn (Conversation $c) => [
            'conversation_id' => $c->id,
            'patient' => $c->patient->name,
            'assunto' => $c->assunto,
            'last_message' => $c->latestMessage?->corpo,
        ];

        return Inertia::render('Professional/Dashboard', [
            'pendingCount' => $pending->count(),
            'pending' => $pending->take(self::LIST_LIMIT)->map($present)->values(),
            'recent' => $conversations
                ->unique('patient_id')
                ->take(self::LIST_LIMIT)
                ->map($present)
                ->values(),
        ]);
    }
}
