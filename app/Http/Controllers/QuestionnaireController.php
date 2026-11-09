<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthQuestionnaireRequest;
use App\Models\HealthQuestionnaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionnaireController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Questionnaire/Form', [
            'options' => [
                'preventivo' => HealthQuestionnaire::PREVENTIVE_OPTIONS,
                'camisinha' => HealthQuestionnaire::CONDOM_OPTIONS,
                'contraceptivo' => HealthQuestionnaire::CONTRACEPTIVE_OPTIONS,
                'vacina' => HealthQuestionnaire::HPV_VACCINE_OPTIONS,
                'historico_alteracao' => HealthQuestionnaire::ALTERATION_HISTORY_OPTIONS,
                'acompanhamento' => HealthQuestionnaire::FOLLOW_UP_OPTIONS,
                'sintomas' => [
                    ...HealthQuestionnaire::SYMPTOM_OPTIONS,
                    HealthQuestionnaire::SYMPTOM_NONE => 'Nenhum desses sintomas',
                    HealthQuestionnaire::SKIPPED => 'Prefiro não responder',
                ],
                'barreiras' => [
                    ...HealthQuestionnaire::BARRIER_OPTIONS,
                    HealthQuestionnaire::BARRIER_NONE => 'Nenhuma dessas dificuldades',
                ],
            ],
        ]);
    }

    public function store(StoreHealthQuestionnaireRequest $request): RedirectResponse
    {
        $questionnaire = $request->user()->healthQuestionnaires()->create($request->validated());

        return redirect()->route('questionnaires.show', $questionnaire);
    }

    public function index(Request $request): Response
    {
        $items = $request->user()->healthQuestionnaires()
            ->latest()
            ->get()
            ->map(function (HealthQuestionnaire $q) {
                $level = $q->analysis()->level;

                return [
                    'id' => $q->id,
                    'created_at' => $q->created_at->toDateString(),
                    'level' => $level->value,
                    'level_label' => $level->label(),
                ];
            });

        return Inertia::render('Questionnaire/Index', ['items' => $items]);
    }

    public function show(Request $request, int $id): Response
    {
        $questionnaire = $request->user()->healthQuestionnaires()->findOrFail($id);
        $analysis = $questionnaire->analysis();

        return Inertia::render('Questionnaire/Show', [
            'questionnaire' => [
                'id' => $questionnaire->id,
                'created_at' => $questionnaire->created_at->toDateString(),
            ],
            'analysis' => [
                'level' => $analysis->level->value,
                'label' => $analysis->level->label(),
                'summary' => $analysis->level->summary(),
                'follow_up' => $analysis->level->followUp(),
                'urgent' => $analysis->urgent,
                'attention_points' => array_map(fn ($f) => $f->toArray(), $analysis->attentionPoints()),
                'guidance' => array_map(fn ($f) => $f->toArray(), $analysis->findings),
            ],
        ]);
    }
}
