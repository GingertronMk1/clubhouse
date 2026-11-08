<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('sports/Index', ['sports' => Sport::with('scoring')->orderBy('name')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('sports/Form');
    }

    public function store(SportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($data): void {
            $sport = Sport::create($data);
            $sport->scoring()->createMany($data['scoring']);
        });

        return to_route('sports.index');
    }

    public function edit(Sport $sport): Response
    {
        return Inertia::render('sports/Form', ['sport' => $sport->load('scoring')]);
    }

    public function update(SportRequest $request, Sport $sport): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($sport, $data): void {
            $sport->update($data);
            $sport->scoring()->delete();
            $sport->scoring()->createMany($data['scoring']);
        });

        return to_route('sports.index');
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return to_route('sports.index');
    }
}
