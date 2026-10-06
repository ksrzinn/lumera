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
            ->map(fn (HealthQuestionnaire $q) => [
                'id' => $q->id,
                'created_at' => $q->created_at->toDateString(),
                'risk' => $q->riskLevel()->value,
                'risk_label' => $q->riskLevel()->label(),
            ]);

        return Inertia::render('Questionnaire/Index', ['items' => $items]);
    }

    public function show(Request $request, int $id): Response
    {
        $questionnaire = $request->user()->healthQuestionnaires()->findOrFail($id);
        $risk = $questionnaire->riskLevel();

        return Inertia::render('Questionnaire/Show', [
            'questionnaire' => [
                'id' => $questionnaire->id,
                'created_at' => $questionnaire->created_at->toDateString(),
            ],
            'risk' => [
                'level' => $risk->value,
                'label' => $risk->label(),
                'orientation' => $risk->orientation(),
            ],
        ]);
    }
}
