<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GamesController extends Controller
{
    public function __construct()
    {
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
        $team_id = $request->query('team');
        $round = $request->query('round');
        $week = $request->query('week');
        $team_query = !is_null($team_id) ? "(home_team_id = $team_id OR away_team_id = $team_id)" : '';
        $week_query = !is_null($week) ? "week = $week" : '';
        $round_query = !is_null($round) ? "round = $round" : '';
        $filter_queries = array_filter( array($team_query, $round_query, $week_query), function($q){return !empty($q);} );
        $filter_string = !empty($filter_queries) ? sprintf( "where %s", join(" AND ", $filter_queries) ) : '';
        // dd($filter_string);
        $query_string = sprintf("select * from games %s", $filter_string);
        $games = DB::select($query_string);
        return view('games', ['games' => $games, 'query_params' => array(
            'team_id'=>$team_id,
            'round'=>$round,
            'week'=>$week
        )]);
    }

    // public function team($team_id)
    // {
    //     $games = DB::select("select * from games where home_team_id = $team_id OR away_team_id = $team_id");

    //     return view('games', ['games' => $games, 'team_id' => $team_id]);
    // }
}