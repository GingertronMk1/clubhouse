<?php

namespace App\Models;

use Database\Factories\SportEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** @property-read GameSportEvent $pivot */
#[Fillable(['name', 'description', 'score'])]
class SportEvent extends Model
{
    /** @return BelongsToMany<Game, $this, GameSportEvent> */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_sport_event')->using(GameSportEvent::class)
            ->withPivot(['id', 'competitor_type', 'competitor_id', 'occurred_at'])->withTimestamps();
    }

    /** @use HasFactory<SportEventFactory> */
    use HasFactory;

    use HasUuids;

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
