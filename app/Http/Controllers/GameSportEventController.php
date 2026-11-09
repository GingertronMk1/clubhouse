<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameSportEventRequest;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;

class GameSportEventController extends Controller
{
    public function store(StoreGameSportEventRequest $request, Game $game): RedirectResponse
    {
        $data = $request->validated();
        $game->sportEvents()->attach($data['sport_event_id'], [
            'competitor_type' => (new ($data['competitor_type'] === 'team' ? Team::class : GameUser::class))->getMorphClass(),
            'competitor_id' => $data['competitor_id'],
            'occurred_at' => $data['occurred_at'],
        ]);

        return to_route('games.show', $game);
    }
}
