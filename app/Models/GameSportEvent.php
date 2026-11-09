<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameSportEvent extends Pivot
{
    use HasUuids;

    /** @return MorphTo<GameUser|Team, $this> */
    public function competitor(): MorphTo
    {
        return $this->morphTo();
    }
}
