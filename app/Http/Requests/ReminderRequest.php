<?php

namespace App\Http\Requests;

use App\Models\Reminder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReminderRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:120'],
            'data_hora' => ['required', 'date'],
            'tipo' => ['required', Rule::in(array_keys(Reminder::TYPE_OPTIONS))],
        ];
    }
}
