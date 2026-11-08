<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/** @property array<array-key, mixed> $result */
#[Fillable(['name', 'start', 'description', 'summary', 'sport_id', 'competition_id', 'club1_id', 'club2_id', 'result', 'club1_score', 'club2_score', 'location_id'])]
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $with = ['sport', 'competition', 'club1', 'club2', 'location'];

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /** @return BelongsTo<Competition, $this> */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function club1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'club1_id');
    }

    /** @return BelongsTo<Team, $this> */
    public function club2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'club2_id');
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['start' => 'datetime', 'result' => 'array', 'club1_score' => 'array', 'club2_score' => 'array'];
    }
}
