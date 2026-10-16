<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public const PASSWORD = 'minhasenha';

    public function run(): void
    {
        User::whereIn('email', ['paciente@cuidar.test', 'profissional@cuidar.test'])->delete();

        $professional = User::create([
            'name' => 'Dra. Marina Souza',
            'email' => 'profissional@cuidar.test',
            'password' => self::PASSWORD,
            'role' => User::ROLE_PROFESSIONAL,
            'data_nascimento' => '1984-08-12',
        ]);

        $patient = User::create([
            'name' => 'Ana Silva',
            'email' => 'paciente@cuidar.test',
            'password' => self::PASSWORD,
            'role' => User::ROLE_PATIENT,
            'data_nascimento' => '1995-04-20',
        ]);

        $this->seedCycles($patient);
        $this->seedReminders($patient);
        $this->seedQuestionnaires($patient);
        $this->seedConversation($patient, $professional);
    }

    private function seedCycles(User $patient): void
    {
        $patient->cycleEntries()->createMany([
            [
                'data_inicio' => now()->subDays(75)->toDateString(),
                'data_fim' => now()->subDays(70)->toDateString(),
                'fluxo' => 'moderado',
                'sintomas' => ['nenhum'],
            ],
            [
                'data_inicio' => now()->subDays(47)->toDateString(),
                'data_fim' => now()->subDays(42)->toDateString(),
                'fluxo' => 'intenso',
                'sintomas' => ['dor_pelvica'],
                'notas' => 'Cólica forte no segundo dia.',
            ],
            [
                'data_inicio' => now()->subDays(19)->toDateString(),
                'data_fim' => now()->subDays(14)->toDateString(),
                'fluxo' => 'leve',
                'sintomas' => ['nenhum'],
            ],
        ]);
    }

    private function seedReminders(User $patient): void
    {
        $patient->reminders()->createMany([
            ['titulo' => 'Consulta de revisão', 'data_hora' => now()->subDays(5)->setTime(9, 0), 'tipo' => 'consulta'],
            ['titulo' => 'Papanicolau', 'data_hora' => now()->addDays(3)->setTime(14, 30), 'tipo' => 'preventivo'],
            ['titulo' => 'Consulta médica', 'data_hora' => now()->addDays(60)->setTime(10, 0), 'tipo' => 'consulta'],
            ['titulo' => 'Vacina HPV', 'data_hora' => now()->subDays(30)->setTime(8, 0), 'tipo' => 'outro', 'concluido' => true],
        ]);
    }

    private function seedQuestionnaires(User $patient): void
    {
        $base = [
            'faixa_etaria' => '25_29',
            'vida_sexual_iniciada' => 'sim',
            'idade_primeira_relacao' => '18_20',
            'possui_colo_utero' => 'sim',
            'lembra_ano_preventivo' => 'nunca',
            'resultado_alterado_anterior' => 'nunca_realizei',
            'acompanhamento_alteracao' => 'nao_se_aplica',
            'teste_hpv' => 'nao',
            'frequencia_consultas' => 'so_receita',
            'usa_contraceptivo' => 'sim',
            'metodos_contraceptivos' => ['pilula'],
            'tempo_uso_contraceptivo' => '1_a_5_anos',
            'uso_prolongado_pilula' => 'sim_menos_5',
            'acredita_anticoncepcional_substitui' => 'nao_sei',
            'situacoes_preservativo' => 'quando_necessario',
            'sabe_preservativo_hpv' => 'ja_ouvi',
            'exames_ist' => 'nunca',
            'diagnostico_hpv' => 'nao',
            'diagnostico_outra_ist' => 'nao',
            'idade_primeira_dose' => 'nunca',
            'sabe_onde_vacinar' => 'duvidas',
            'acredita_vacina_elimina_rastreio' => 'nao_sei',
            'sintomas' => ['nenhum'],
        ];

        $older = $patient->healthQuestionnaires()->make([
            ...$base,
            'fez_preventivo' => 'nao',
            'quando_ultimo_preventivo' => 'nunca',
            'resultado_preventivo' => 'nunca',
            'usa_camisinha' => 'nunca',
            'vacinada_hpv' => 'nao',
            'barreiras' => ['falta_informacao', 'vergonha'],
        ]);
        $older->created_at = now()->subMonths(6);
        $older->save();

        $patient->healthQuestionnaires()->create([
            ...$base,
            'faixa_etaria' => '30_39',
            'fez_preventivo' => 'sim',
            'quando_ultimo_preventivo' => '1_a_2_anos',
            'lembra_ano_preventivo' => 'sim_ano',
            'resultado_preventivo' => 'normal',
            'resultado_alterado_anterior' => 'nao',
            'frequencia_consultas' => 'anual',
            'acredita_anticoncepcional_substitui' => 'nao',
            'usa_camisinha' => 'as_vezes',
            'sabe_preservativo_hpv' => 'sim',
            'exames_ist' => 'sim_12_meses',
            'vacinada_hpv' => 'completou',
            'idade_primeira_dose' => '9_14',
            'sabe_onde_vacinar' => 'sim',
            'acredita_vacina_elimina_rastreio' => 'nao',
            'barreiras' => ['nenhuma'],
        ]);
    }

    private function seedConversation(User $patient, User $professional): void
    {
        $conversation = new Conversation([
            'professional_id' => $professional->id,
            'assunto' => 'Dúvida sobre meu exame',
        ]);
        $conversation->patient_id = $patient->id;
        $conversation->save();

        $messages = [
            [$professional, 'Olá, Ana! Em que posso ajudar hoje?', now()->subMinutes(30)],
            [$patient, 'Tenho uma dúvida sobre o meu exame preventivo. Com que frequência devo repetir?', now()->subMinutes(20)],
        ];

        foreach ($messages as [$sender, $body, $at]) {
            $message = $conversation->messages()->make(['sender_id' => $sender->id, 'corpo' => $body]);
            $message->created_at = $at;
            $message->save();
        }
    }
}
