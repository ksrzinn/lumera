<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConversationRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConversationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['patient:id,name', 'professional:id,name', 'latestMessage'])
            ->orderByRaw("status = 'open' desc")
            ->latest('updated_at')
            ->get()
            ->map(fn (Conversation $c) => [
                'id' => $c->id,
                'assunto' => $c->assunto,
                'status' => $c->status,
                'with' => $user->id === $c->patient_id ? $c->professional->name : $c->patient->name,
                'last_message' => $c->latestMessage?->corpo,
                'awaiting_reply' => $c->isOpen() && $c->latestMessage?->sender_id === $c->patient_id
                    && $user->id === $c->professional_id,
            ]);

        return Inertia::render('Messages/Index', ['conversations' => $conversations]);
    }

    public function create(): Response
    {
        return Inertia::render('Messages/Create', [
            'professionals' => User::where('role', User::ROLE_PROFESSIONAL)->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StoreConversationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $conversation = new Conversation([
            'professional_id' => $data['professional_id'],
            'assunto' => $data['assunto'],
        ]);
        $conversation->patient_id = $request->user()->id;
        $conversation->save();

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'corpo' => $data['corpo'],
        ]);

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Request $request, int $id): Response
    {
        $user = $request->user();
        $conversation = $user->conversations()->with(['patient:id,name', 'professional:id,name'])->findOrFail($id);

        return Inertia::render('Messages/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'assunto' => $conversation->assunto,
                'status' => $conversation->status,
                'with' => $user->id === $conversation->patient_id
                    ? $conversation->professional->name
                    : $conversation->patient->name,
                'can_close' => $user->id === $conversation->professional_id && $conversation->isOpen(),
            ],
            'messages' => $conversation->messages()->orderBy('id')->get()->map(fn (Message $m) => [
                'id' => $m->id,
                'mine' => $m->sender_id === $user->id,
                'corpo' => $m->corpo,
                'sent_at' => $m->created_at->isToday() ? $m->created_at->format('H:i') : $m->created_at->format('d/m H:i'),
            ]),
        ]);
    }

    public function close(Request $request, int $id): RedirectResponse
    {
        $conversation = $request->user()->conversations()->where('professional_id', $request->user()->id)->findOrFail($id);
        $conversation->update(['status' => Conversation::STATUS_CLOSED]);

        return redirect()->route('conversations.index');
    }
}
