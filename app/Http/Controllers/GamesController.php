<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Game;


class GamesController extends Controller
{
    public function __construct()
    {
        #NOTE where is the best place to set constants?  --> this should not be a constant

        if (!defined('TEAMS_BY_ID')){
            $teams = DB::select("SELECT team_id, team_name FROM teams;");
            $teams_by_id = array();
            foreach($teams as $team_data){
                $teams_by_id[$team_data->team_id] = $team_data->team_name;
            };
            define('TEAMS_BY_ID', $teams_by_id);
            define('WEEKS_IN_ROUND', count(TEAMS_BY_ID) - 1 );
            define('WEEKS_COUNT', WEEKS_IN_ROUND * 2 );
        }
    }
    public function index(Request $request)
    {
        $team_id = $request->query('team_id');
        $round = $request->query('round');
        $week = $request->query('week');
    
        $games = Game::query()
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
        return view('games', ['games' => $games, 'query_params' => array(
            'team_id'=>$team_id,
            'round'=>$round,
            'week'=>$week
        )]);
    }
}