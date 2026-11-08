<?php

namespace App\Models;

use Database\Factories\GameScoringEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['game_id', 'scoring_event_id', 'timestamp'])]
class GameScoringEvent extends Model
{
    /** @use HasFactory<GameScoringEventFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $with = ['game', 'scoringEvent'];

    /** @return BelongsTo<Game, $this> */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /** @return BelongsTo<ScoringEvent, $this> */
    public function scoringEvent(): BelongsTo
    {
        return $this->belongsTo(ScoringEvent::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['timestamp' => 'integer'];
    }
}
