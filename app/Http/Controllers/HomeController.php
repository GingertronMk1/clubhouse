<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Sport;
use Illuminate\Http\Request;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        return inertia('Home', [
            'counts' => [
                'sport' => [
                    'count' => Sport::query()->count(),
                    'link' => route('sport.index'),
                ],
                'location' => [
                    'count' => Location::query()->count(),
                    'link' => route('location.index'),
                ],
            ],
        ]);
    }
}
