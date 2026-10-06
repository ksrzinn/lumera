<?php

namespace App\Http\Requests;

use App\Models\HealthQuestionnaire;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHealthQuestionnaireRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idade' => ['required', 'integer', 'between:9,120'],
            'fez_preventivo' => ['required', Rule::in(array_keys(HealthQuestionnaire::PREVENTIVE_OPTIONS))],
            'data_ultimo_preventivo' => ['nullable', 'required_if:fez_preventivo,sim', 'date', 'before_or_equal:today'],
            'usa_camisinha' => ['required', Rule::in(array_keys(HealthQuestionnaire::CONDOM_OPTIONS))],
            'metodo_contraceptivo' => ['nullable', Rule::in(array_keys(HealthQuestionnaire::CONTRACEPTIVE_OPTIONS))],
            'vacinada_hpv' => ['required', Rule::in(array_keys(HealthQuestionnaire::HPV_VACCINE_OPTIONS))],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if ($key === null && ($data['fez_preventivo'] ?? null) !== 'sim') {
            $data['data_ultimo_preventivo'] = null;
        }

        return $data;
    }
}
