<?php

namespace App\Support\Anamnesis;

use App\Enums\CareLevel;

final readonly class AnamnesisResult
{
    /**
     * @param  list<Finding>  $findings  ordenados do mais para o menos importante
     */
    public function __construct(
        public CareLevel $level,
        public bool $urgent,
        public array $findings,
    ) {}

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        return array_map(fn (Finding $finding) => $finding->code, $this->findings);
    }

    /**
     * Achados que merecem atenção (exclui os meramente informativos).
     *
     * @return list<Finding>
     */
    public function attentionPoints(): array
    {
        return array_values(array_filter(
            $this->findings,
            fn (Finding $finding) => $finding->level !== CareLevel::EmDia,
        ));
    }
}
