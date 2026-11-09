<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GameUserSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::query()->without('sport')->orderBy('sort_order')->orderBy('name')->orderBy('id')->get()->groupBy('sport_id');

        Game::query()->without(['sport', 'competition', 'location'])
            ->with(['team1.club.users', 'team2.club.users', 'users'])
            ->chunkById(100, function ($games) use ($positions): void {
                $rows = [];
                $now = now();

                foreach ($games as $game) {
                    /** @var Collection<int, Position>|null $sportPositions */
                    $sportPositions = $positions->get($game->sport_id);
                    if (! $sportPositions || $sportPositions->isEmpty()) {
                        continue;
                    }

                    $assigned = $game->users->modelKeys();
                    $secondCandidates = $game->team2?->club?->users->whereNotIn('id', $assigned) ?? collect();
                    $firstCandidateIds = $game->team1?->club?->users->modelKeys() ?? [];
                    $secondCandidates = $secondCandidates->sortBy('email');
                    $reservedSecondPlayer = $game->users->contains(fn ($user) => $user->pivot->team === 2)
                        ? null : ($secondCandidates->first(fn ($user) => ! in_array($user->id, $firstCandidateIds, true)) ?? $secondCandidates->first())?->id;

                    foreach ([1, 2] as $side) {
                        if ($game->users->contains(fn ($user) => $user->pivot->team === $side)) {
                            continue;
                        }

                        $team = $side === 1 ? $game->team1 : $game->team2;
                        $candidates = $team?->club?->users->whereNotIn('id', $assigned) ?? collect();
                        if ($side === 1 && $reservedSecondPlayer !== null) {
                            $candidates = $candidates->where('id', '!=', $reservedSecondPlayer);
                        }
                        if ($candidates->isEmpty()) {
                            continue;
                        }

                        $availablePositions = $sportPositions->values();
                        foreach ($candidates->sortBy('email')->take($sportPositions->count()) as $player) {
                            $position = $availablePositions->shift();
                            $rows[] = [
                                'game_id' => $game->id,
                                'user_id' => $player->id,
                                'team' => $side,
                                'position_id' => $position->id,
                                'jersey_number' => $position->default_number,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            $assigned[] = $player->id;
                        }
                    }
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('game_user')->insert($chunk);
                }
            });
    }
}
