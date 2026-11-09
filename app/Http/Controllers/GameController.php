<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'sport' => ['nullable', 'uuid'],
            'team' => ['nullable', 'uuid'],
            'club' => ['nullable', 'uuid'],
        ]);

        $games = Game::query()->with(['team1', 'team2', 'competition', 'location']);
        if (! empty($filters['sport'])) {
            $games->where('sport_id', $filters['sport']);
        }
        if (! empty($filters['team'])) {
            $games->where(fn (Builder $query) => $query->where('team1_id', $filters['team'])->orWhere('team2_id', $filters['team']));
        }
        if (! empty($filters['club'])) {
            $games->where(fn (Builder $query) => $query
                ->whereHas('team1', fn (Builder $team) => $team->where('club_id', $filters['club']))
                ->orWhereHas('team2', fn (Builder $team) => $team->where('club_id', $filters['club'])));
        }

        return Inertia::render('games/Index', [
            'records' => $games->orderBy('start')->orderBy('id')->paginate(20)->withQueryString(),
            'filters' => $filters,
            'sports' => Sport::query()->orderBy('name')->get(['id', 'name']),
            'teams' => Team::query()->with('club:id,name')->orderBy('name')->get(['id', 'name', 'club_id']),
            'clubs' => Club::query()->orderBy('name')->get(['id', 'name']),
        ]);
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
        $game->load(['sport', 'competition', 'team1.club', 'team2.club', 'location', 'users', 'sportEvents']);
        $positions = Position::query()->withTrashed()->without('sport')->whereIn('id', $game->users->pluck('pivot.position_id'))->get()->keyBy('id');
        $lineups = $game->users->sortBy('name')->values()->map(fn ($user) => [
            'id' => $user->id,
            'game_user_id' => $user->pivot->id,
            'name' => $user->name,
            'team' => $user->pivot->team,
            'number' => $user->pivot->jersey_number,
            'position' => $positions->get($user->pivot->position_id)?->only(['name', 'preview_x', 'preview_y']),
        ]);
        $events = $game->sportEvents->map(fn ($event) => [
            'id' => $event->pivot->id,
            'name' => $event->name,
            'occurred_at' => $event->pivot->occurred_at->toIso8601String(),
            'competitor' => $event->pivot->competitor_type === (new Team)->getMorphClass()
                ? collect([$game->team1, $game->team2])->firstWhere('id', $event->pivot->competitor_id)?->name
                : $lineups->firstWhere('game_user_id', $event->pivot->competitor_id)['name'] ?? null,
        ])->sortBy('occurred_at')->values();
        $game->unsetRelation('users');
        $game->unsetRelation('sportEvents');

        return Inertia::render('games/Show', [
            'record' => $game,
            'lineups' => $lineups,
            'events' => $events,
            'sportEvents' => $game->sport->events()->orderBy('name')->get(['id', 'name', 'score']),
        ]);
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
