<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['titulo', 'data_hora', 'tipo', 'concluido'])]
class Reminder extends Model
{
    public const UPCOMING_DAYS = 7;

    public const TYPE_OPTIONS = [
        'preventivo' => 'Preventivo',
        'consulta' => 'Consulta',
        'outro' => 'Outro',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
            'concluido' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * concluido, vencido (passou e não foi concluído), proximo (nos próximos dias) ou futuro.
     */
    public function status(): string
    {
        if ($this->concluido) {
            return 'concluido';
        }

        if ($this->data_hora->isPast()) {
            return 'vencido';
        }

        return $this->data_hora->lte(now()->addDays(self::UPCOMING_DAYS)) ? 'proximo' : 'futuro';
    }
}
