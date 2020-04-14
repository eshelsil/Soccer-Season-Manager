<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoresController extends Controller
{

    public function index(Request $request)
    { 
        $set_game_id = $request->query('set_game_id');
        $selected_tab = $request->query('tab') ?? 'unplayed';
        $round = $request->query('round');
        $week = $request->query('week');
        $team_id = $request->query('team_id');
        
        $is_done = ($selected_tab == 'unplayed') ? 0 : 1;
        // $available_games = DB::table('games')->where('is_done', $is_done)->get();
        // $no_available_games = count($available_games) == 0;
        $has_available_games = DB::table('games')->where('is_done', $is_done)->exists();
        
        if ($has_available_games){
            $games = DB::table('games')
                ->where('is_done', $is_done)
                ->where(function($query) use($week, $round) {
                    if (!is_null($week)){
                        $query->where('week', $week);
                    }
                    if (!is_null($round)){
                        $query->where('round', $round);
                    }
                })
                ->where(function($query) use($team_id) {
                    if (!is_null($team_id)){
                        $query->where('home_team_id', $team_id)
                            ->orWhere('away_team_id', $team_id);
                    }
                })
                ->get();
        } else {
            $games = null;
        }


        // $where_conditions = [];
        // array_push($where_conditions, ['is_done', '=', $is_done]);
        // if (!is_null($week)){
        //     array_push($where_conditions, ['week', '=', $week]);
        // }
        // if (!is_null($round)){
        //     array_push($where_conditions, ['round', '=', $round]);
        // }
        // if (!is_null($team_id)){
        //     $games = Game::query()
        //         ->where($where_conditions)
        //         ->where(function($query) use($team_id, $week, $round) {
        //             $query->where()
        //             if (!is_null($week)){
        //                 array_push($where_conditions, ['week', '=', $week]);
        //             }
        //             if (!is_null($round)){
        //                 array_push($where_conditions, ['round', '=', $round]);
        //             }
        //             $query->where('home_team_id', $team_id)
        //                 ->orWhere('away_team_id', $team_id);
        //         })
        //         ->get();
        //         #NOTE anyway to push team_id condition into where conditions?
        // } else {
        //     $games = DB::table('games')->where($where_conditions)->get();
        // }


        // $games = DB::table('games')
        //         ->where('is_done', $is_done)
        //         ->when(!is_null($week), function($query) use($week) {
        //             $query->where('week', $week);
        //         })
        //         ->when(!is_null($round), function($query) use($round) {
        //             $query->where('round', $round);
        //         })
        //         ->when(!is_null($team_id), function($query) use($team_id) {
        //             $query->where('home_team_id', $team_id)
        //                 ->orWhere('away_team_id', $team_id);
        //         })
        //         ->get();


        $teams_by_id = ManageController::get_teams_by_id();
        $weeks_count = ( count($teams_by_id) - 1 ) * 2;
        return view('set_scores', [
            'query_params' => array(
                'round'=>$round,
                'week'=>$week,
                'team_id'=>$team_id,
                'set_game_id' => $set_game_id
            ),
            'selected_tab' => $selected_tab,
            'games' => $games,
            'has_available_games' => $has_available_games,
            'teams_by_id' => $teams_by_id,
            'weeks_count' => $weeks_count
        ]);
    }

    public function randomize_game_scores(){
        return $this->randomize_game_scores_as_should();
        # handle no games_table
        $game_ids = DB::table('games')->where('is_done', 0)->pluck('game_id');
        $goals_options = range(0,4);
        foreach($game_ids as $game_id){
            #NOTE should use transaction here?  -->  yes

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

    public function randomize_game_scores_as_should(){
        # handle no games_table
        $game_ids = DB::table('games')->where('is_done', 0)->pluck('game_id');
        $goals_options = range(0,4);
        $teams_by_id = ManageController::get_teams_by_id();
        $relevant_id = array_search('Hapoel Tel Aviv', $teams_by_id);
        foreach($game_ids as $game_id){
            $home_score = $goals_options[array_rand($goals_options, 1)];
            $away_score = $goals_options[array_rand($goals_options, 1)];
            $game_from_db = DB::table('games')->where('game_id', $game_id)->first();
            if ($game_from_db->home_team_id == $relevant_id && $home_score < 2){
                $home_score = $goals_options[array_rand($goals_options, 1)];
            }
            if ($game_from_db->away_team_id == $relevant_id && $away_score < 2){
                $away_score = $goals_options[array_rand($goals_options, 1)];
            }
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
