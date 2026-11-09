<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('games/Index', ['records' => Game::query()->with(['team1', 'team2', 'competition', 'location'])->orderBy('start')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('games/Form', $this->formOptions());
    }

    public function store(GameRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $lineup = $data['lineup'] ?? [];
        unset($data['lineup']);
        $data['score'] = [];

        $game = DB::transaction(function () use ($data, $lineup) {
            $game = Game::query()->create($data);
            $this->syncLineup($game, $lineup);

            return $game;
        });

        return to_route('games.show', $game);
    }

    public function show(Game $game): Response
    {
        return Inertia::render('games/Show', ['record' => $game->load(['sport', 'competition', 'team1.club', 'team2.club', 'location'])]);
    }

    public function edit(Game $game): Response
    {
        return Inertia::render('games/Form', [...$this->formOptions(), 'record' => $game->load('users'), 'start' => $game->start ? Carbon::parse($game->getRawOriginal('start'))->format('Y-m-d\TH:i:s') : null]);
    }

    public function update(GameRequest $request, Game $game): RedirectResponse
    {
        $data = $request->validated();
        $lineup = $data['lineup'] ?? [];
        unset($data['lineup']);
        $data['score'] = [];
        DB::transaction(function () use ($game, $data, $lineup): void {
            $game->update($data);
            $this->syncLineup($game, $lineup);
        });

        return to_route('games.show', $game);
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return to_route('games.index');
    }

    /** @param array<int, array{user_id: string, team: int, position_id: string, jersey_number?: int|null}> $lineup */
    private function syncLineup(Game $game, array $lineup): void
    {
        $game->users()->sync(collect($lineup)->mapWithKeys(fn (array $player) => [$player['user_id'] => [
            'team' => $player['team'],
            'position_id' => $player['position_id'],
            'jersey_number' => $player['jersey_number'] ?? null,
        ]])->all());
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'sports' => Sport::query()->orderBy('name')->get(['id', 'name']),
            'competitions' => Competition::query()->orderBy('name')->get(['id', 'name']),
            'positions' => Position::query()->orderBy('sort_order')->get(['id', 'name', 'sport_id']),
            'teams' => Team::query()->with(['club:id,name', 'club.users:id,name'])->orderBy('name')->get(['id', 'name', 'club_id']),
            'locations' => Location::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
