<?php

use App\Models\Game;
use App\Models\Scoring;
use App\Models\ScoringBreakdown;
use Database\Seeders\ClubSeeder;
use Database\Seeders\CompetitionSeeder;
use Database\Seeders\GameSeeder;
use Database\Seeders\SportSeeder;
use Database\Seeders\TeamSeeder;

test('inference uses the fewest events and requires a touchdown for conversions', function () {
    $methods = collect([
        new Scoring(['name' => 'Touchdown', 'value' => 6]),
        new Scoring(['name' => 'Field goal', 'value' => 3]),
        new Scoring(['name' => 'Safety', 'value' => 2]),
        new Scoring(['name' => 'Extra point', 'value' => 1]),
        new Scoring(['name' => 'Two-point conversion', 'value' => 2]),
    ]);

    $thirteen = collect(ScoringBreakdown::infer($methods, 13))->keyBy('name');
    $two = collect(ScoringBreakdown::infer($methods, 2))->keyBy('name');

    expect($thirteen['Touchdown']['count'])->toBe(2);
    expect($thirteen['Extra point']['count'])->toBe(1);
    expect($thirteen->sum('count'))->toBe(3);
    expect($two['Safety']['count'])->toBe(1);
    expect($two['Two-point conversion']['count'])->toBe(0);
});

test('inference handles zero and impossible totals without inventing scoring methods', function () {
    $methods = collect([new Scoring(['name' => 'Goal', 'value' => 3])]);

    expect(ScoringBreakdown::infer($methods, 0))->toBe([['name' => 'Goal', 'value' => 3, 'count' => 0]]);
    expect(ScoringBreakdown::infer($methods, 1))->toBeNull();
});

test('fixture seeding infers each known result and preserves recorded breakdowns', function () {
    $this->seed([SportSeeder::class, ClubSeeder::class, TeamSeeder::class, CompetitionSeeder::class, GameSeeder::class]);
    $games = Game::query()->without(['sport', 'competition', 'club1', 'club2', 'location'])->get();

    foreach ($games as $game) {
        foreach (['club1' => 'home', 'club2' => 'away'] as $club => $side) {
            $score = $game->{$club.'_score'};
            if ($game->result === []) {
                expect($score)->toBe([]);

                continue;
            }
            expect(collect($score)->sum(fn (array $method): int => $method['value'] * $method['count']))->toBe($game->result[$side]);
            expect($game->{$club.'_score_source'})->toBe('inferred');
        }
    }

    $game = $games->firstWhere('description', 'nfl fixture 1');
    $custom = [['name' => 'Field goal', 'value' => 3, 'count' => 2]];
    $game->update(['club1_score' => $custom]);
    $game->setAttribute('club1_score_source', null)->save();
    $this->seed(GameSeeder::class);

    expect($game->fresh()->club1_score)->toBe($custom);
    expect($game->fresh()->club1_score_source)->toBeNull();
});
