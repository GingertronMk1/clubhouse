<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('sports/Index', ['sports' => Sport::query()->orderBy('name')->get(['id', 'slug', 'name', 'description'])->map(fn (Sport $sport) => [
            'id' => $sport->id,
            'slug' => $sport->slug,
            'name' => $sport->name,
            'description' => Str::limit($sport->description, 160),
        ])]);
    }

    public function create(): Response
    {
        return Inertia::render('sports/Form');
    }

    public function store(SportRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $sport = Sport::query()->create($request->safe()->except('events'));
            /** @var array<int, array{name: string, description: ?string, score: int}> $events */
            $events = $request->validated('events');
            $sport->events()->createMany(collect($events)->map(fn (array $event) => [...$event, 'description' => $event['description'] ?? ''])->all());
        });

        return to_route('sports.index');
    }

    public function show(Sport $sport): Response
    {
        return Inertia::render('sports/Show', [
            'sport' => $sport->load('events'),
            'positions' => $sport->positions()->orderBy('sort_order')->orderBy('name')->get(['id', 'slug', 'name', 'preview_x', 'preview_y', 'default_number']),
        ]);
    }

    public function edit(Sport $sport): Response
    {
        return Inertia::render('sports/Form', ['sport' => $sport->load('events')]);
    }

    public function update(SportRequest $request, Sport $sport): RedirectResponse
    {
        DB::transaction(function () use ($request, $sport) {
            $sport->update($request->safe()->except('events'));
            $sport->events()->delete();
            /** @var array<int, array{name: string, description: ?string, score: int}> $events */
            $events = $request->validated('events');
            $sport->events()->createMany(collect($events)->map(fn (array $event) => [...$event, 'description' => $event['description'] ?? ''])->all());
        });

        return to_route('sports.index');
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return to_route('sports.index');
    }
}
