<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReminderController extends Controller
{
    public function index(Request $request): Response
    {
        $reminders = $request->user()->reminders()
            ->orderBy('concluido')
            ->orderBy('data_hora')
            ->get()
            ->map(fn (Reminder $reminder) => $this->present($reminder));

        return Inertia::render('Reminders/Index', [
            'reminders' => $reminders,
            'typeOptions' => Reminder::TYPE_OPTIONS,
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(ReminderRequest $request): RedirectResponse
    {
        $request->user()->reminders()->create($request->validated());

        return redirect()->route('reminders.index');
    }

    public function edit(Request $request, int $id): Response
    {
        return $this->form($request->user()->reminders()->findOrFail($id));
    }

    public function update(ReminderRequest $request, int $id): RedirectResponse
    {
        $request->user()->reminders()->findOrFail($id)->update($request->validated());

        return redirect()->route('reminders.index');
    }

    public function toggle(Request $request, int $id): RedirectResponse
    {
        $reminder = $request->user()->reminders()->findOrFail($id);
        $reminder->update(['concluido' => ! $reminder->concluido]);

        return back();
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $request->user()->reminders()->findOrFail($id)->delete();

        return redirect()->route('reminders.index');
    }

    private function form(?Reminder $reminder): Response
    {
        return Inertia::render('Reminders/Form', [
            'reminder' => $reminder ? $this->present($reminder) : null,
            'typeOptions' => Reminder::TYPE_OPTIONS,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Reminder $reminder): array
    {
        return [
            'id' => $reminder->id,
            'titulo' => $reminder->titulo,
            'data_hora' => $reminder->data_hora->format('Y-m-d\TH:i'),
            'relative' => $reminder->data_hora->diffForHumans(),
            'tipo' => $reminder->tipo,
            'concluido' => $reminder->concluido,
            'status' => $reminder->status(),
        ];
    }
}
