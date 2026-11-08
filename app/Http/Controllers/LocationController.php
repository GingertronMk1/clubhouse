<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('directory/Index', ['kind' => 'locations', 'records' => Location::query()->orderBy('name')->get()]);
    }

    public function create(): Response
    {
        return Inertia::render('directory/Form', ['kind' => 'locations']);
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['additional_information'] = json_decode($data['additional_information'], true);

        $location = Location::create($data);

        return redirect()->route('locations.show', $location);
    }

    public function show(Location $location): Response
    {
        return Inertia::render('directory/Show', ['kind' => 'locations', 'record' => $location]);
    }

    public function edit(Location $location): Response
    {
        return Inertia::render('directory/Form', ['kind' => 'locations', 'record' => $location]);
    }

    public function update(LocationRequest $request, Location $location): RedirectResponse
    {
        $data = $request->validated();
        $data['additional_information'] = json_decode($data['additional_information'], true);

        $location->update($data);

        return redirect()->route('locations.show', $location);
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('locations.index');
    }
}
