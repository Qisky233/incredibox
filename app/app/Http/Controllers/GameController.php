<?php

namespace App\Http\Controllers;

use App\Models\GList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    public function show($title)
    {
        $game = GList::where('title', $title)->first();
        if (!$game) {
            return Inertia::render('NotFound', [
                'message' => 'Game not found'
            ]);
        }

        return Inertia::render('Detail', [
            'game' => $game
        ]);
    }
}
