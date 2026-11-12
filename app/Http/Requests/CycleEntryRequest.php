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
            'sintomas_outros' => [
                Rule::requiredIf(fn () => $this->hasOtherSymptom()),
                'nullable',
                'string',
                'max:255',
            ],
            'notas' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sintomas_outros.required' => 'Descreva o sintoma.',
        ];
    }

    /**
     * Descarta o texto de "Outros" quando a opção não está marcada.
     */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated();

        if (! $this->hasOtherSymptom()) {
            $data['sintomas_outros'] = null;
        }

        return data_get($data, $key, $default);
    }

    private function hasOtherSymptom(): bool
    {
        return in_array('outros', (array) $this->input('sintomas', []), true);
    }
}
