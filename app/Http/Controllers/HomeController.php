<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Sport;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'sports' => Sport::query()->orderBy('name')->get(['id', 'name', 'description']),
            'clubs' => Club::query()->orderBy('name')->get(['id', 'name', 'description']),
        ]);
    }
}
