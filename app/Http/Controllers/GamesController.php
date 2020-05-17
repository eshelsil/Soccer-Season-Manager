<?php

namespace App\Http\Controllers;

use App\Game;

class GamesController extends Controller
{
    public function index()
    {
        return view('games',[
            'teams_by_id' => app('RegisterationManager')->get_teams_by_id(),
            'has_games' => Game::exists()
        ]);
    }
}