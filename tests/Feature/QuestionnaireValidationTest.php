<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionnaireValidationTest extends TestCase
{
    use RefreshDatabase;

    private function submit(array $overrides): \Illuminate\Testing\TestResponse
    {
        $user = User::factory()->patient()->create();

        return $this->actingAs($user)->postJson(route('questionnaires.store'), array_merge([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => '2025-01-01',
            'historico_alteracao' => 'nao',
            'usa_camisinha' => 'sempre',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'sim',
            'sintomas' => ['nenhum'],
            'barreiras' => ['nenhuma'],
        ], $overrides));
    }

    public function test_valid_answers_are_accepted(): void
    {
        $this->submit([])->assertRedirect();
    }

    public function test_nested_arrays_are_rejected_without_server_error(): void
    {
        $this->submit(['sintomas' => [['x'], 'nenhum']])->assertUnprocessable()->assertJsonValidationErrors('sintomas.0');
        $this->submit(['barreiras' => [['a'], ['b']]])->assertUnprocessable()->assertJsonValidationErrors('barreiras.0');
    }

    public function test_keyed_arrays_are_rejected(): void
    {
        $this->submit(['sintomas' => ['a' => 'dor_pelvica_persistente']])->assertUnprocessable();
    }

    public function test_exclusive_option_cannot_be_mixed(): void
    {
        $this->submit(['sintomas' => ['nenhum', 'dor_pelvica_persistente']])->assertUnprocessable()->assertJsonValidationErrors('sintomas');
        $this->submit(['barreiras' => ['nenhuma', 'medo']])->assertUnprocessable()->assertJsonValidationErrors('barreiras');
    }
}
