<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return inertia('Sports/Index', ['sports' => Sport::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Sports/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSportRequest $request): RedirectResponse
    {
        $sport = Sport::query()->create($request->validated());

        return redirect()->route('sports.show', $sport);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sport $sport): Response
    {
        return inertia('Sports/Show', ['sport' => $sport]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sport $sport): Response
    {
        return inertia('Sports/Edit', ['sport' => $sport]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSportRequest $request, Sport $sport): RedirectResponse
    {
        $sport->update($request->validated());

        return redirect()->route('sports.show', $sport);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sport $sport): RedirectResponse
    {
        $sport->delete();

        return redirect()->route('sports.index');
    }
}
