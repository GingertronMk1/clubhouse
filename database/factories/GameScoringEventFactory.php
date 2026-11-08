<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameScoringEvent;
use App\Models\ScoringEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GameScoringEvent> */
class GameScoringEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'scoring_event_id' => fn (array $attributes) => ScoringEvent::factory()->for(Game::query()->whereKey($attributes['game_id'])->firstOrFail()->sport),
            'timestamp' => null,
        ];
    }
}
