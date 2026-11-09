<?php

namespace App\Support\Anamnesis;

use App\Enums\CareLevel;

final readonly class Finding
{
    public function __construct(
        public string $code,
        public CareLevel $level,
    ) {}

    public function title(): string
    {
        return __("anamnese.{$this->code}.title");
    }

    public function guidance(): string
    {
        return __("anamnese.{$this->code}.guidance");
    }

    /**
     * @return array{code: string, level: string, title: string, guidance: string}
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'level' => $this->level->value,
            'title' => $this->title(),
            'guidance' => $this->guidance(),
        ];
    }
}
