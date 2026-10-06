<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request, int $id): RedirectResponse
    {
        $conversation = $request->user()->conversations()->findOrFail($id);

        if (! $conversation->isOpen()) {
            throw ValidationException::withMessages(['corpo' => 'Esta conversa foi encerrada.']);
        }

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'corpo' => $request->validated('corpo'),
        ]);
        $conversation->touch();

        return back();
    }
}
