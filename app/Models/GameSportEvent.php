<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $occurred_at
 * @property string|null $description
 */
class GameSportEvent extends Pivot
{
    use HasUuids;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    /** @return MorphTo<Model, $this> */
    public function competitor(): MorphTo
    {
        return $this->morphTo();
    }
}
