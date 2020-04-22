<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Game;
use App\Team;

class ScoresController extends Controller
{
    private $teams_by_id = null;

    public function get_teams_by_id(){
        if (is_null($this->teams_by_id)){
            $teams = Team::query()->get();
            $teams_by_id = array();
            foreach($teams as $team_data){
                $teams_by_id[$team_data->team_id] = $team_data->team_name;
            };
            $this->teams_by_id = $teams_by_id;
        }
        return $this->teams_by_id;
    }

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
        $has_available_games = Game::query()->where('is_done', $is_done)->exists();
        
        if ($has_available_games){
            $games = Game::query()
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


        $teams_by_id = $this->get_teams_by_id();
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
        $games_to_set = Game::query()->where('is_done', 0)->get();
        $goals_options = range(0,4);
        return DB::transaction(function () use($games_to_set, $goals_options) {
            foreach($games_to_set as $game){
                $home_score = $goals_options[array_rand($goals_options, 1)];
                $away_score = $goals_options[array_rand($goals_options, 1)];
                $game->home_score = $home_score;
                $game->away_score = $away_score;
                $game->update();
            }
        });
    }

    public function randomize_game_scores_as_should(){
        # handle no games_table
        $games_to_set = Game::query()->where('is_done', 0)->get();
        $goals_options = range(0,4);
        $teams_by_id = $this->get_teams_by_id();
        $relevant_id = array_search('Hapoel Tel Aviv', $teams_by_id);
        return DB::transaction(function () use($games_to_set, $goals_options, $relevant_id) {
            foreach($games_to_set as $game){
                $home_score = $goals_options[array_rand($goals_options, 1)];
                $away_score = $goals_options[array_rand($goals_options, 1)];
                if ($game->getTeamSide($relevant_id) == 'home' && $home_score < 2){
                    $home_score = $goals_options[array_rand($goals_options, 1)];
                }
                if ($game->getTeamSide($relevant_id) == 'away' && $away_score < 2){
                    $away_score = $goals_options[array_rand($goals_options, 1)];
                }
                $game->home_score = $home_score;
                $game->away_score = $away_score;
                $game->update();
            }
        });
    }

    public function reset_all(){
        return Game::where('is_done', 1)
            ->update(
                ['home_score' => null, 'away_score' => null]
            );
    }
    
    public function reset_score($game_id){
        return Game::find($game_id)
            ->update(
                ['home_score' => null, 'away_score' => null]
            );
    }
    
    public function update_score(Request $request, $game_id){
        $home_score = $request->input()['home'];
        $away_score = $request->input()['away'];
        return Game::find($game_id)
            ->update(
                ['home_score' => $home_score, 'away_score' => $away_score]
            );
    }
    

}
