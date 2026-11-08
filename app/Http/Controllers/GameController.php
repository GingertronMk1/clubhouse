<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(): Response
    {
        $games = Game::query()->with(['scoringEvents' => fn ($query) => $query->without(['game', 'team'])->with(['scoringEvent' => fn ($query) => $query->without('sport')])])->orderBy('start')->get();

        return Inertia::render('games/Index', ['records' => $games->map(function (Game $game): Game {
            $game->setAttribute('score_totals', $this->scoreTotals($game));
            $game->unsetRelation('scoringEvents');

            return $game;
        })]);
    }

    public function create(): Response
    {
        return Inertia::render('games/Form', $this->formOptions());
    }

    public function store(GameRequest $request): RedirectResponse
    {
        $game = DB::transaction(function () use ($request): Game {
            $data = $request->validated();
            $game = Game::query()->create([...Arr::except($data, ['scoring_events']), 'score' => []]);
            $this->saveScoringEvents($game, $data['scoring_events'] ?? []);

            return $game;
        });

        return to_route('games.show', $game);
    }

    public function show(Game $game): Response
    {
        $game->load(['sport', 'competition', 'club1.club', 'club2.club', 'location', 'scoringEvents' => fn ($query) => $query->without('game')->with(['scoringEvent' => fn ($query) => $query->without('sport'), 'team' => fn ($query) => $query->without('club')])->orderBy('timestamp')]);

        return Inertia::render('games/Show', ['record' => $game, 'scoreTotals' => $this->scoreTotals($game)]);
    }

    public function edit(Game $game): Response
    {
        return Inertia::render('games/Form', [...$this->formOptions(), 'record' => $game->load(['scoringEvents' => fn ($query) => $query->without(['game', 'team'])->with(['scoringEvent' => fn ($query) => $query->without('sport')])]), 'start' => $game->start ? Carbon::parse($game->getRawOriginal('start'))->format('Y-m-d\TH:i:s') : null]);
    }

    public function update(GameRequest $request, Game $game): RedirectResponse
    {
        DB::transaction(function () use ($request, $game): void {
            $data = $request->validated();
            $game->update(Arr::except($data, ['scoring_events']));
            if (array_key_exists('scoring_events', $data)) {
                $this->saveScoringEvents($game, $data['scoring_events']);
            }
        });

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
            'sports' => Sport::query()->with(['scoringEvents' => fn ($query) => $query->withTrashed()->without('sport')])->orderBy('name')->get(['id', 'name']),
            'competitions' => Competition::query()->orderBy('name')->get(['id', 'name']),
            'teams' => Team::query()->with('club:id,name')->orderBy('name')->get(['id', 'name', 'club_id']),
            'locations' => Location::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    /** @param list<array{id?: ?string, scoring_event_id: string, team_id?: ?string, timestamp?: ?int}> $events */
    private function saveScoringEvents(Game $game, array $events): void
    {
        $ids = [];
        foreach ($events as $event) {
            $attributes = Arr::only($event, ['scoring_event_id', 'team_id', 'timestamp']);
            if (! empty($event['id'])) {
                $scoringEvent = $game->scoringEvents()->without(['game', 'scoringEvent', 'team'])->findOrFail($event['id']);
                $scoringEvent->update($attributes);
            } else {
                $scoringEvent = $game->scoringEvents()->create($attributes);
            }
            $ids[] = $scoringEvent->id;
        }
        $game->scoringEvents()->whereNotIn('id', $ids)->delete();
    }

    /** @return array{club1: int, club2: int}|null */
    private function scoreTotals(Game $game): ?array
    {
        $scores = ['club1' => 0, 'club2' => 0];
        $hasAssignedEvents = false;
        foreach ($game->scoringEvents as $event) {
            if ($event->team_id === null || $event->scoringEvent === null) {
                return null;
            }
            if ($event->team_id === $game->club1_id) {
                $scores['club1'] += $event->scoringEvent->value;
                $hasAssignedEvents = true;
            } elseif ($event->team_id === $game->club2_id) {
                $scores['club2'] += $event->scoringEvent->value;
                $hasAssignedEvents = true;
            } else {
                return null;
            }
        }

        return $hasAssignedEvents ? $scores : null;
    }
}
