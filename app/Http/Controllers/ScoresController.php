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
        $round = $request->query('round');
        $week = $request->query('week');
        $team_id = $request->query('team_id');
        $is_done = $request->query('is_done') == 1 ? 1 : 0;
        
        $has_available_games = Game::query()->where('is_done', $is_done)->exists();
        
        $teams_manager = app('RegisteredTeamsManager');
        return view('set_scores', [
            'query_params' => array(
                'round'=>$round,
                'week'=>$week,
                'team_id'=>$team_id,
                'is_done'=>$is_done
            ),
            'has_available_games' => $has_available_games,
            'teams_by_id' => $teams_manager->get_teams_by_id(),
            'weeks_count' => $teams_manager->get_weeks_count()
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
