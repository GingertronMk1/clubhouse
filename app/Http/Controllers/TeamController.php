<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Club;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Club $club): Response
    {
        return Inertia::render('teams/Index', [
            'club' => $club,
            'teams' => $club->teams()->orderBy('name')->get(['id', 'name', 'description'])->map(fn (Team $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'description' => Str::limit($team->description, 160),
            ]),
        ]);
    }

    public function create(Club $club): Response
    {
        return Inertia::render('teams/Form', [
            'club' => $club,
        ]);
    }

    public function store(TeamRequest $request, Club $club): RedirectResponse
    {
        $club->teams()->create($request->validated());

        return to_route('clubs.teams.index', $club);
    }

    public function show(Club $club, Team $team): Response
    {
        return Inertia::render('teams/Show', [
            'club' => $club,
            'team' => $team,
        ]);
    }

    public function edit(Club $club, Team $team): Response
    {
        return Inertia::render('teams/Form', [
            'club' => $club,
            'team' => $team,
        ]);
    }

    public function update(TeamRequest $request, Club $club, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return to_route('clubs.teams.index', $club);
    }

    public function destroy(Club $club, Team $team): RedirectResponse
    {
        $team->delete();

        return to_route('clubs.teams.index', $club);
    }
}
