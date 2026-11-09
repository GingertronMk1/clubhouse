<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Position;
use Inertia\Inertia;
use Inertia\Response;

class LiveGameController extends Controller
{
    public function __invoke(Game $game): Response
    {
        $game->load(['team1.club', 'team2.club', 'users', 'sportEvents']);
        $positions = Position::query()->withTrashed()->without('sport')->whereIn('id', $game->users->pluck('pivot.position_id'))->get(['id', 'name'])->keyBy('id');

        return Inertia::render('LiveGame', [
            'game' => [
                ...$game->only(['id', 'name', 'team1_id', 'team2_id']),
                'team1' => $game->team1 ? ['name' => $game->team1->name, 'club' => $game->team1->club?->only(['name'])] : null,
                'team2' => $game->team2 ? ['name' => $game->team2->name, 'club' => $game->team2->club?->only(['name'])] : null,
                'users' => $game->users->map(fn ($user) => [
                    'name' => $user->name,
                    'position' => $positions->get($user->pivot->position_id)?->name,
                    'pivot' => $user->pivot->only(['id', 'team', 'jersey_number']),
                ]),
                'sport_events' => $game->sportEvents->map(fn ($event) => [
                    ...$event->only(['name', 'score']),
                    'pivot' => $event->pivot->only(['id', 'competitor_type', 'competitor_id', 'occurred_at', 'description']),
                ]),
            ],
        ]);
    }
}
