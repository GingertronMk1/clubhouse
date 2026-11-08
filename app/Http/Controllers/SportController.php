<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
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
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $sport = Sport::query()->create(Arr::only($data, ['name', 'description']));
            $this->saveScoringEvents($sport, $data['scoring']);
        });

        return to_route('sports.index');
    }

    public function show(Sport $sport): Response
    {
        return Inertia::render('sports/Show', [
            'sport' => $this->sportWithScoring($sport),
            'positions' => $sport->positions()->orderBy('sort_order')->orderBy('name')->get(['id', 'slug', 'name', 'preview_x', 'preview_y', 'default_number']),
        ]);
    }

    public function edit(Sport $sport): Response
    {
        return Inertia::render('sports/Form', ['sport' => $this->sportWithScoring($sport)]);
    }

    public function update(SportRequest $request, Sport $sport): RedirectResponse
    {
        DB::transaction(function () use ($request, $sport): void {
            $data = $request->validated();
            $sport->update(Arr::only($data, ['name', 'description']));
            $this->saveScoringEvents($sport, $data['scoring']);
        });

        return to_route('sports.index');
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return to_route('sports.index');
    }

    /** @return array<string, mixed> */
    private function sportWithScoring(Sport $sport): array
    {
        return [...$sport->toArray(), 'scoring' => $sport->scoringEvents()->without('sport')->get(['id', 'name', 'value'])];
    }

    /** @param list<array{id?: ?string, name: string, value: int}> $events */
    private function saveScoringEvents(Sport $sport, array $events): void
    {
        $ids = [];
        foreach ($events as $sortOrder => $event) {
            $attributes = ['name' => $event['name'], 'value' => $event['value'], 'sort_order' => $sortOrder];
            if (! empty($event['id'])) {
                $scoringEvent = $sport->scoringEvents()->without('sport')->findOrFail($event['id']);
                $scoringEvent->update($attributes);
            } else {
                $scoringEvent = $sport->scoringEvents()->create($attributes);
            }
            $ids[] = $scoringEvent->id;
        }
        $sport->scoringEvents()->whereNotIn('id', $ids)->delete();
    }
}
