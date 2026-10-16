<?php

namespace App\Support\Anamnesis;

/**
 * Acesso à definição do questionário em config/questionnaire.php.
 */
final class QuestionnaireDefinition
{
    /**
     * @return list<array{title: string, questions: list<array<string, mixed>>}>
     */
    public static function sections(): array
    {
        return config('questionnaire.sections');
    }

    /**
     * @return list<string>
     */
    public static function fields(): array
    {
        return array_map(fn (array $question) => $question['field'], self::questions());
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function questions(): array
    {
        return array_merge(...array_map(fn (array $section) => $section['questions'], self::sections()));
    }

    /**
     * @return array<string, mixed>
     */
    public static function question(string $field): array
    {
        foreach (self::questions() as $question) {
            if ($question['field'] === $field) {
                return $question;
            }
        }

        throw new \InvalidArgumentException("Pergunta desconhecida: {$field}");
    }

    /**
     * @return array<string, string> valor => rótulo
     */
    public static function options(string $field): array
    {
        return self::question($field)['options'];
    }
}
