<?php

namespace App\Http\Controllers;

use App\Models\CycleEntry;
use App\Models\HealthQuestionnaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $questionnaires = $user->healthQuestionnaires()->get()->map(fn (HealthQuestionnaire $q) => [
            'kind' => 'questionnaire',
            'key' => "q{$q->id}",
            'date' => $q->created_at->toDateString(),
            'title' => 'Questionário de saúde',
            'detail' => $q->riskLevel()->label(),
            'risk' => $q->riskLevel()->value,
            'href' => route('questionnaires.show', $q->id),
        ]);

        $cycles = $user->cycleEntries()->get()->map(fn (CycleEntry $c) => [
            'kind' => 'cycle',
            'key' => "c{$c->id}",
            'date' => $c->data_inicio->toDateString(),
            'title' => 'Registro de ciclo',
            'detail' => $c->data_fim ? 'Até '.$c->data_fim->format('d/m/Y') : 'Em andamento',
            'risk' => null,
            'href' => route('cycle.edit', $c->id),
        ]);

        return Inertia::render('History/Index', [
            'items' => $questionnaires->concat($cycles)->sortByDesc('date')->values(),
        ]);
    }
}
