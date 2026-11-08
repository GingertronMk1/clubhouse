<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Models\Position;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function index(Sport $sport): Response
    {
        return Inertia::render('positions/Index', [
            'sport' => $sport,
            'positions' => $sport->positions()->orderBy('sort_order')->orderBy('name')->get(['id', 'slug', 'name', 'description'])->map(fn (Position $position) => [
                'id' => $position->id,
                'slug' => $position->slug,
                'name' => $position->name,
                'description' => Str::limit($position->description, 160),
            ]),
        ]);
    }

    public function create(Sport $sport): Response
    {
        return Inertia::render('positions/Form', [
            'sport' => $sport,
            'otherPositions' => $sport->positions()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'preview_x', 'preview_y', 'default_number']),
        ]);
    }

    public function store(PositionRequest $request, Sport $sport): RedirectResponse
    {
        $sport->positions()->create($request->validated());

        return to_route('sports.positions.index', $sport);
    }

    public function show(Sport $sport, Position $position): Response
    {
        return Inertia::render('positions/Show', [
            'sport' => $sport,
            'position' => $position,
            'otherPositions' => $sport->positions()->whereKeyNot($position->getKey())->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'preview_x', 'preview_y', 'default_number']),
        ]);
    }

    public function edit(Sport $sport, Position $position): Response
    {
        return Inertia::render('positions/Form', [
            'sport' => $sport,
            'position' => $position,
            'otherPositions' => $sport->positions()->whereKeyNot($position->getKey())->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'preview_x', 'preview_y', 'default_number']),
        ]);
    }

    public function update(PositionRequest $request, Sport $sport, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        return to_route('sports.positions.index', $sport);
    }

    public function destroy(Sport $sport, Position $position): RedirectResponse
    {
        $position->delete();

        return to_route('sports.positions.index', $sport);
    }
}
