<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompetitionRequest;
use App\Models\Competition;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('competitions/Index', ['records' => Competition::query()->with('location')->orderBy('name')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('competitions/Form', $this->formOptions());
    }

    public function store(CompetitionRequest $request): RedirectResponse
    {
        return to_route('competitions.show', Competition::query()->create($request->validated()));
    }

    public function show(Competition $competition): Response
    {
        return Inertia::render('competitions/Show', ['record' => $competition->load(['location', 'parent', 'children', 'games.team1', 'games.team2'])]);
    }

    public function edit(Competition $competition): Response
    {
        return Inertia::render('competitions/Form', [...$this->formOptions(), 'record' => $competition]);
    }

    public function update(CompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->update($request->validated());

        return to_route('competitions.show', $competition);
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return to_route('competitions.index');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'locations' => Location::query()->orderBy('name')->get(['id', 'name']),
            'competitions' => Competition::query()->without(['parent', 'location'])->orderBy('name')->get(['id', 'name']),
        ];
    }
}
