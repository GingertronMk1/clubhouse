<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClubRequest;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ClubController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Club::class);

        return Inertia::render('clubs/Index', ['records' => Club::query()->orderBy('name')->get()]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Club::class);

        return Inertia::render('clubs/Form');
    }

    public function store(ClubRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $club = Club::query()->create($data);

        return redirect()->route('clubs.show', $club);
    }

    public function show(Club $club): Response
    {
        Gate::authorize('view', $club);

        return Inertia::render('clubs/Show', ['record' => $club, 'teams' => $club->teams()->without('club')->orderBy('name')->get(['id', 'slug', 'name'])]);
    }

    public function edit(Club $club): Response
    {
        Gate::authorize('update', $club);

        return Inertia::render('clubs/Form', ['record' => $club]);
    }

    public function update(ClubRequest $request, Club $club): RedirectResponse
    {
        $data = $request->validated();
        $club->update($data);

        return redirect()->route('clubs.show', $club);
    }

    public function destroy(Club $club): RedirectResponse
    {
        Gate::authorize('delete', $club);

        $club->delete();

        return redirect()->route('clubs.index');
    }
}
