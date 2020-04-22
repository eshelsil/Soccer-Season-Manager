<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Game;
use App\Team;


class TeamsController extends Controller
{
    public function __construct()
    {
        $this->ensure_teams_table_existance();
    }

    private function ensure_teams_table_existance(){
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function(Blueprint $table){
                $table->increments('team_id');
                $table->string('team_name', 50)->unique();
            });
        }
    }
    
    private function get_teams_by_id(){
        $teams = Team::query()->get();
        $teams_by_id = array();
        foreach($teams as $team_data){
            $teams_by_id[$team_data->team_id] = $team_data->team_name;
        };
        return $teams_by_id;
    }

    public function index(Request $request){
        return view('set_teams', ['teams_by_id' => $this->get_teams_by_id()]);
    }

    public static function truncate_teams_table(){
        return Team::query()->delete();
    }
    
    public static function delete_team($team_id){
        //https://laravel.com/docs/7.x/controllers  --> read about api restful implementation
        if (Game::query()->exists()) {
            return response("Deleting a team is not allwed when \"games\" table is not empty", 400);
        }
        #NOTE will this return bad errors if the error is returned from sql? see add_team as well
        return Team::find($team_id)->delete();
    }

    public static function add_team(Request $request){
        #NOTE craete validations on backend for example team_name length  --> https://laravel.com/docs/7.x/validation
        if (Game::query()->exists()) {
            return response("Adding a team is not allwed when \"games\" table is not empty", 400);
        }
        $team_name = $request->input()['name'];
        return Team::insert(['team_name' => $team_name]);
    }
    
    public function set_teams(Request $request){
        #verify no games table
        if (Game::query()->exists()) {
            return response("Adding/Removing teams is not allwed when \"games\" table is not empty", 400);
        }
        $this->truncate_teams_table();
        $teams = $request->input('teams') ?? array();
        return DB::transaction(function () use($teams) {
            foreach($teams as $team){
                Team::insert(["team_name" => $team]);
                #handle faliure
            }
        });
    }
}
