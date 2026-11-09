<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<array-key, mixed> $score
 * @property-read Collection<int, User&object{pivot: GameUser}> $users
 */
#[Fillable(['name', 'start', 'description', 'summary', 'sport_id', 'competition_id', 'team1_id', 'team2_id', 'score', 'location_id'])]
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $with = ['sport', 'competition', 'team1', 'team2', 'location'];

    /** @return BelongsToMany<User, $this, GameUser> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(GameUser::class)->withPivot(['id', 'team', 'position_id', 'jersey_number'])->withTimestamps();
    }

    /** @return BelongsToMany<SportEvent, $this, GameSportEvent> */
    public function sportEvents(): BelongsToMany
    {
        return $this->belongsToMany(SportEvent::class, 'game_sport_event')->using(GameSportEvent::class)
            ->withPivot(['id', 'competitor_type', 'competitor_id'])->withTimestamps();
    }

    /** @return BelongsToMany<User, $this, GameUser> */
    public function team1Users(): BelongsToMany
    {
        return $this->users()->withPivotValue('team', 1);
    }

    /** @return BelongsToMany<User, $this, GameUser> */
    public function team2Users(): BelongsToMany
    {
        return $this->users()->withPivotValue('team', 2);
    }

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
    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    /** @return BelongsTo<Team, $this> */
    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['start' => 'datetime', 'score' => 'array'];
    }
}
