<?php

namespace App\Http\Requests;

use App\Support\Anamnesis\QuestionnaireDefinition;
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
        $rules = [];

        foreach (QuestionnaireDefinition::questions() as $question) {
            $field = $question['field'];
            $allowed = Rule::in(array_keys($question['options']));
            $condition = isset($question['show_if'])
                ? ['nullable', "required_if:{$question['show_if']['field']},{$question['show_if']['equals']}"]
                : ['required'];

            if ($question['type'] === 'checkbox') {
                $rules[$field] = [...$condition, 'array', 'list', 'min:1'];
                $rules["{$field}.*"] = ['distinct', $allowed];
            } else {
                $rules[$field] = [...$condition, $allowed];
            }
        }

        return $rules;
    }

    /**
     * Opções "nenhum" / "prefiro não responder" não combinam com outras.
     *
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach (QuestionnaireDefinition::questions() as $question) {
                    if ($question['type'] === 'checkbox') {
                        $this->rejectExclusiveMix($validator, $question['field'], $question['exclusive'] ?? []);
                    }
                }
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

        if (count($selected) > 1 && array_intersect($selected, $exclusive) !== []) {
            $validator->errors()->add($field, 'Escolha apenas uma opção quando selecionar uma resposta como "nenhum", "não sei" ou "prefiro não responder".');
        }
    }

    /**
     * Perguntas ocultas pela condição de exibição não guardam resposta.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if ($key === null) {
            foreach (QuestionnaireDefinition::questions() as $question) {
                $condition = $question['show_if'] ?? null;

                if ($condition !== null && ($data[$condition['field']] ?? null) !== $condition['equals']) {
                    $data[$question['field']] = null;
                }
            }
        }

        return $data;
    }
}
