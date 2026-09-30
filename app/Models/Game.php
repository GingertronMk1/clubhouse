<?php

namespace App\Models;

use App\HasLocation;
use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    use HasLocation;
    use HasUuids;
    use SoftDeletes;

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function club1(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club1_id');
    }

    public function club2(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club2_id');
    }
}
