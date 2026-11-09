<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class LiveGameController extends Controller
{
    public function __invoke(Game $game): Response
    {
        return Inertia::render('LiveGame', [
            'game' => $game->only(['id', 'name']),
        ]);
    }
}
