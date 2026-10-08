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
        $older = $patient->healthQuestionnaires()->make([
            'idade' => 29,
            'fez_preventivo' => 'nao',
            'data_ultimo_preventivo' => null,
            'usa_camisinha' => 'nunca',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'nao',
        ]);
        $older->created_at = now()->subMonths(6);
        $older->save();

        $patient->healthQuestionnaires()->create([
            'idade' => 30,
            'fez_preventivo' => 'sim',
            'data_ultimo_preventivo' => now()->subYears(2)->toDateString(),
            'usa_camisinha' => 'as_vezes',
            'metodo_contraceptivo' => 'pilula',
            'vacinada_hpv' => 'sim',
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
