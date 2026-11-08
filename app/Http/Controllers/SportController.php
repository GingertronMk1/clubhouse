<?php

namespace App\Http\Controllers;

use App\Http\Requests\SportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('sports/Index', ['sports' => Sport::orderBy('name')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('sports/Form');
    }

    public function store(SportRequest $request): RedirectResponse
    {
        Sport::create($request->validated());

        return to_route('sports.index');
    }

    public function edit(Sport $sport): Response
    {
        return Inertia::render('sports/Form', ['sport' => $sport]);
    }

    public function update(SportRequest $request, Sport $sport): RedirectResponse
    {
        $sport->update($request->validated());

        return to_route('sports.index');
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return to_route('sports.index');
    }
}
