<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Support\PreventiveStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $pending = $user->reminders()->where('concluido', false);

        $nextReminder = (clone $pending)->where('data_hora', '>=', now())->orderBy('data_hora')->first();
        $lastCycle = $user->cycleEntries()->orderByDesc('data_inicio')->first();

        return Inertia::render('Patient/Dashboard', [
            'overdueCount' => (clone $pending)->where('data_hora', '<', now())->count(),
            'nextReminder' => $nextReminder ? [
                'titulo' => $nextReminder->titulo,
                'data_hora' => $nextReminder->data_hora->format('Y-m-d\TH:i'),
                'relative' => $nextReminder->data_hora->diffForHumans(),
                'status' => $nextReminder->status(),
            ] : null,
            'lastCycle' => $lastCycle ? [
                'data_inicio' => $lastCycle->data_inicio->toDateString(),
                'data_fim' => $lastCycle->data_fim?->toDateString(),
            ] : null,
            'preventive' => PreventiveStatus::for($user),
        ]);
    }
}
