<?php

namespace App\Http\Requests;

use App\Models\CycleEntry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CycleEntryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data_inicio' => ['required', 'date', 'before_or_equal:today'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio', 'before_or_equal:today'],
            'fluxo' => ['nullable', Rule::in(array_keys(CycleEntry::FLOW_OPTIONS))],
            'sintomas' => ['nullable', 'array'],
            'sintomas.*' => [Rule::in(array_keys(CycleEntry::SYMPTOM_OPTIONS))],
            'notas' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
