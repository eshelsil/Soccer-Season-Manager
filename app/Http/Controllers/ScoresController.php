<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoresController extends Controller
{

    public function index(Request $request)
    { 
        $games = DB::table('games')->where('is_done', 0)->get();
        return view('set_scores', [
            'unplayed_games' => $games,
            'teams_by_id' => ManageController::get_teams_by_id()
        ]);
    }

    public function randomize_game_scores(){
        # handle no games_table
        $game_ids = DB::table('games')->where('is_done', 0)->pluck('game_id');
        $goals_options = range(0,4);
        foreach($game_ids as $game_id){
            $home_score = $goals_options[array_rand($goals_options, 1)];
            $away_score = $goals_options[array_rand($goals_options, 1)];
            DB::table('games')
                ->where('game_id', $game_id)
                ->update(
                ['home_score' => $home_score, 'away_score' => $away_score]
            );
        }
        return response(200);
        
    }

    public function reset_all(){
        return DB::table('games')->where('is_done', 1)
            ->update(
                ['home_score' => null, 'away_score' => null]
            );
    }
}
