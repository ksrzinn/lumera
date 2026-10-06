<?php

namespace App\Http\Controllers;

use App\Http\Requests\CycleEntryRequest;
use App\Models\CycleEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CycleEntryController extends Controller
{
    public function index(Request $request): Response
    {
        $entries = $request->user()->cycleEntries()
            ->orderByDesc('data_inicio')
            ->get()
            ->map(fn (CycleEntry $entry) => $this->present($entry));

        return Inertia::render('Cycle/Index', [
            'entries' => $entries,
            'flowOptions' => CycleEntry::FLOW_OPTIONS,
            'symptomOptions' => CycleEntry::SYMPTOM_OPTIONS,
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(CycleEntryRequest $request): RedirectResponse
    {
        $request->user()->cycleEntries()->create($request->validated());

        return redirect()->route('cycle.index');
    }

    public function edit(Request $request, int $id): Response
    {
        return $this->form($request->user()->cycleEntries()->findOrFail($id));
    }

    public function update(CycleEntryRequest $request, int $id): RedirectResponse
    {
        $request->user()->cycleEntries()->findOrFail($id)->update($request->validated());

        return redirect()->route('cycle.index');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $request->user()->cycleEntries()->findOrFail($id)->delete();

        return redirect()->route('cycle.index');
    }

    private function form(?CycleEntry $entry): Response
    {
        return Inertia::render('Cycle/Form', [
            'entry' => $entry ? $this->present($entry) : null,
            'flowOptions' => CycleEntry::FLOW_OPTIONS,
            'symptomOptions' => CycleEntry::SYMPTOM_OPTIONS,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(CycleEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'data_inicio' => $entry->data_inicio->toDateString(),
            'data_fim' => $entry->data_fim?->toDateString(),
            'fluxo' => $entry->fluxo,
            'sintomas' => $entry->sintomas ?? [],
            'notas' => $entry->notas,
        ];
    }
}
