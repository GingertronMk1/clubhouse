<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('games/Index', ['records' => Game::query()->with(['club1', 'club2', 'competition', 'location'])->orderBy('start')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('games/Form', $this->formOptions());
    }

    public function store(GameRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['score'] = json_decode($data['score'], true);

        return to_route('games.show', Game::query()->create($data));
    }

    public function show(Game $game): Response
    {
        return Inertia::render('games/Show', ['record' => $game->load(['sport', 'competition', 'club1.club', 'club2.club', 'location'])]);
    }

    public function edit(Game $game): Response
    {
        return Inertia::render('games/Form', [...$this->formOptions(), 'record' => $game, 'start' => Carbon::parse($game->getRawOriginal('start'))->format('Y-m-d\TH:i:s')]);
    }

    public function update(GameRequest $request, Game $game): RedirectResponse
    {
        $data = $request->validated();
        $data['score'] = json_decode($data['score'], true);
        $game->update($data);

        return to_route('games.show', $game);
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return to_route('games.index');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'sports' => Sport::query()->orderBy('name')->get(['id', 'name']),
            'competitions' => Competition::query()->orderBy('name')->get(['id', 'name']),
            'teams' => Team::query()->with('club:id,name')->orderBy('name')->get(['id', 'name', 'club_id']),
            'locations' => Location::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
