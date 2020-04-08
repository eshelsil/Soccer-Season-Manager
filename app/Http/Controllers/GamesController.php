<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GamesController extends Controller
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
        }
    }
    public function index()
    { 
        $games = DB::select('select * from games');
        return view('games', ['games' => $games]);
    }
}