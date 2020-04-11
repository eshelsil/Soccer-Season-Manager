<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        #NOTE should be defined from advanced
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

    public function index(Request $request){
        #NOTE improve query (use laravel)
        $week = $request->query('week');
        $filter_string = !is_null($week) ? sprintf( "AND %s",  "week <= $week") : '';
        $query_string = sprintf("select * from games where is_done = 1 %s", $filter_string);
        $games = DB::select($query_string);
        $last_game = DB::table('games')->where('is_done', 1)->orderBy('week', 'desc')->first();
        $last_week = (is_null($last_game)) ? null : $last_game->week;
        return view('table', [
            'games' => $games,
            'last_week'=>$last_week,
            'query_params'=>array(
                'week'=>$week
            )
        ]);
    }
}
