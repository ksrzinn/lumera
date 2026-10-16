<?php

namespace Tests\Unit;

use App\Http\Requests\StoreHealthQuestionnaireRequest;
use App\Support\Anamnesis\QuestionnaireDefinition;
use Tests\TestCase;

class QuestionnaireDefinitionTest extends TestCase
{
    public function test_the_questionnaire_has_all_27_clinical_questions_in_order(): void
    {
        $this->assertSame(
            [
                'faixa_etaria', 'vida_sexual_iniciada', 'idade_primeira_relacao', 'possui_colo_utero',
                'fez_preventivo', 'quando_ultimo_preventivo', 'lembra_ano_preventivo', 'resultado_preventivo',
                'resultado_alterado_anterior', 'acompanhamento_alteracao', 'teste_hpv', 'frequencia_consultas',
                'usa_contraceptivo', 'metodos_contraceptivos', 'tempo_uso_contraceptivo', 'uso_prolongado_pilula',
                'acredita_anticoncepcional_substitui',
                'usa_camisinha', 'situacoes_preservativo', 'sabe_preservativo_hpv', 'exames_ist',
                'diagnostico_hpv', 'diagnostico_outra_ist',
                'vacinada_hpv', 'idade_primeira_dose', 'sabe_onde_vacinar', 'acredita_vacina_elimina_rastreio',
            ],
            array_slice(QuestionnaireDefinition::fields(), 0, 27),
        );
    }

    public function test_symptoms_and_barriers_follow_the_27_questions(): void
    {
        $this->assertSame(['sintomas', 'barreiras'], array_slice(QuestionnaireDefinition::fields(), 27));
    }

    public function test_every_question_has_a_label_and_options(): void
    {
        foreach (QuestionnaireDefinition::sections() as $section) {
            foreach ($section['questions'] as $question) {
                $this->assertNotSame('', $question['label'], $question['field']);
                $this->assertNotEmpty($question['options'], $question['field']);
            }
        }
    }

    public function test_the_form_request_validates_every_question(): void
    {
        $rules = (new StoreHealthQuestionnaireRequest)->rules();

        foreach (QuestionnaireDefinition::fields() as $field) {
            $this->assertArrayHasKey($field, $rules);
        }
    }

    public function test_the_first_sexual_intercourse_question_only_shows_after_yes(): void
    {
        $question = QuestionnaireDefinition::question('idade_primeira_relacao');

        $this->assertSame(['field' => 'vida_sexual_iniciada', 'equals' => 'sim'], $question['show_if']);
    }
}
