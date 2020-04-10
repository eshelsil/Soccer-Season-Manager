<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoresController extends Controller
{

    public function index(Request $request, $selected_tab = 'unplayed')
    { 
        $set_game_id = $request->query()['set_game_id'] ?? null;
        if ($selected_tab == 'unplayed'){
            $games = DB::table('games')->where('is_done', 0)->get();
        } else {
            $games = DB::table('games')->where('is_done', 1)->get();
        }
        return view('set_scores', [
            'selected_tab' => $selected_tab,
            'games' => $games,
            'set_game_id' => $set_game_id,
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
    
    public function reset_score($game_id){
        return DB::table('games')->where('game_id', $game_id)
            ->update(
                ['home_score' => null, 'away_score' => null]
            );
    }
    
    public function update_score(Request $request, $game_id){
        $home_score = $request->input()['home'];
        $away_score = $request->input()['away'];
        return DB::table('games')->where('game_id', $game_id)
            ->update(
                ['home_score' => $home_score, 'away_score' => $away_score]
            );
    }
    

}
