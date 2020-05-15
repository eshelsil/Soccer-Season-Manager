<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Game;
use App\Team;

class ScoresController extends Controller
{

    public function index(Request $request)
    { 
        $is_done = $request->query('is_done') == 1;  
        $has_available_games = Game::query()->where('is_done', $is_done)->exists();
        $regist_manager = app('RegisterationManager');
        return view('set_scores', [
            'is_on_done_tab'=>$is_done,
            'has_available_games' => $has_available_games,
            'teams_by_id' => $regist_manager->get_teams_by_id(),
        ]);
    }    

}
