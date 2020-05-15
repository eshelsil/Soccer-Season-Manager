<?php

namespace App\Http\Controllers;

use App\Game;

class TableController extends Controller
{
    public function index(){
        $games = Game::where('is_done', 1)->get();
        return view('table', [
            'games' => $games,
            'teams_by_id' => app('RegisterationManager')->get_teams_by_id()
        ]);
    }
}
