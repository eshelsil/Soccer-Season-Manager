<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        if (!defined('TEAMS_BY_ID')){
            $groups = DB::select("SELECT group_id, group_name FROM groups;");
            $groups_by_id = array();
            foreach($groups as $group_data){
                $groups_by_id[$group_data->group_id] = $group_data->group_name;
            };
            define('TEAMS_BY_ID', $groups_by_id);
            define('WEEKS_IN_ROUND', count(TEAMS_BY_ID) - 1 );
            define('WEEKS_COUNT', WEEKS_IN_ROUND * 2 );
        }
    }

    public function index(Request $request){
        $week = $request->query('week');
        $filter_string = !is_null($week) ? sprintf( "AND %s",  "week <= $week") : '';
        $query_string = sprintf("select * from games where is_done = 1 %s", $filter_string);
        $games = DB::select($query_string);
        return view('table', ['games' => $games, 'query_params'=>array(
            'week'=>$week
        )]);
    }
}
