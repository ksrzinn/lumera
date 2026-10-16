<?php

namespace App\Http\Requests;

use App\Models\HealthQuestionnaire;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'data_ultimo_preventivo' => ['nullable', 'date', 'before_or_equal:today'],
            'historico_alteracao' => ['required', Rule::in(array_keys(HealthQuestionnaire::ALTERATION_HISTORY_OPTIONS))],
            'acompanhamento_alteracao' => ['nullable', 'required_if:historico_alteracao,sim', Rule::in(array_keys(HealthQuestionnaire::FOLLOW_UP_OPTIONS))],
            'usa_camisinha' => ['required', Rule::in(array_keys(HealthQuestionnaire::CONDOM_OPTIONS))],
            'metodo_contraceptivo' => ['nullable', Rule::in(array_keys(HealthQuestionnaire::CONTRACEPTIVE_OPTIONS))],
            'vacinada_hpv' => ['required', Rule::in(array_keys(HealthQuestionnaire::HPV_VACCINE_OPTIONS))],
            'sintomas' => ['required', 'array', 'list', 'min:1'],
            'sintomas.*' => ['distinct', Rule::in([
                ...array_keys(HealthQuestionnaire::SYMPTOM_OPTIONS),
                HealthQuestionnaire::SYMPTOM_NONE,
                HealthQuestionnaire::SKIPPED,
            ])],
            'barreiras' => ['required', 'array', 'list', 'min:1'],
            'barreiras.*' => ['distinct', Rule::in([
                ...array_keys(HealthQuestionnaire::BARRIER_OPTIONS),
                HealthQuestionnaire::BARRIER_NONE,
            ])],
        ];
    }

    /**
     * "Nenhum" e "prefiro não responder" não combinam com outras opções.
     *
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $this->rejectExclusiveMix($validator, 'sintomas', [HealthQuestionnaire::SYMPTOM_NONE, HealthQuestionnaire::SKIPPED]);
                $this->rejectExclusiveMix($validator, 'barreiras', [HealthQuestionnaire::BARRIER_NONE]);
            },
        ];
    }

    /**
     * @param  list<string>  $exclusive
     */
    private function rejectExclusiveMix(Validator $validator, string $field, array $exclusive): void
    {
        if ($validator->errors()->has($field) || $validator->errors()->has("{$field}.*")) {
            return;
        }

        $selected = $this->input($field);

        if (count($selected) < 2) {
            return;
        }

        if (array_intersect($selected, $exclusive) !== []) {
            $validator->errors()->add($field, 'Escolha apenas uma opção quando selecionar "nenhum" ou "prefiro não responder".');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if ($key === null) {
            if (($data['fez_preventivo'] ?? null) !== 'sim') {
                $data['data_ultimo_preventivo'] = null;
            }

            if (($data['historico_alteracao'] ?? null) !== 'sim') {
                $data['acompanhamento_alteracao'] = null;
            }
        }

        return $data;
    }
}
