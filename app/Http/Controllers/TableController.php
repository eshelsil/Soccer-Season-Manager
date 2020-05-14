<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Team;
use App\Game;

class TableController extends Controller
{
    public function index(){
        $where_conditions = [['is_done', '=', 1]];
        $games = Game::where('is_done', 1)->get();
        return view('table', [
            'games' => $games,
            'teams_by_id' => app('RegisteredTeamsManager')->get_teams_by_id()
        ]);
    }
}
