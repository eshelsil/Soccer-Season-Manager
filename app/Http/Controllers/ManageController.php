<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ManageController extends Controller
{
    private function is_all_games_scheduled(){
        $teams_count = DB::table('groups')->count();
        $games_count = DB::table('games')->count();
        return $games_count >= $teams_count * ( $teams_count - 1 );
    }

    private function get_teams_by_id(){
        $teams = DB::table('teams')->get();
        $teams_by_id = array();
        foreach($teams as $team_data){
            $teams_by_id[$team_data->team_id] = $team_data->team_name;
        };
        return $teams_by_id;
    }

    public function home(Request $request)
    { 
        $games_table = Schema::hasTable('games');    
        if($games_table){
            if ($this->is_all_games_scheduled()){
                    return redirect('table');
            }
        }
        return redirect('manage');
    }

    public function index(Request $request){
        $games_table_exists = Schema::hasTable('games');
        $teams_table_exists = Schema::hasTable('groups');
        if (!$games_table_exists || !$teams_table_exists){
            return $this->show_set_teams();
        }
        if (!$this->is_all_games_scheduled()){
            return "schedule games view";
        }
        return 'set scores';
    }

    public function show_set_teams(){
        $this->create_teams_table();
        return view('set_teams', ['teams_by_id' => $this->get_teams_by_id()]);
    }


    
    public static function create_teams_table(){
        if (Schema::hasTable('groups')) {
            return "Table already exists";
        }
        return Schema::create('groups', function(Blueprint $table){
            $table->increments('group_id');
            $table->string('group_name', 50)->unique();
        });
        
    }
    
    public static function drop_teams_table(){
        if (!Schema::hasTable('groups')) {
            return "\"groups\" table does not exist";
        }
        return Schema::drop('groups');
    }
    
    public function add_teams(Request $request){
        #verify no games table
        $teams = $request->input('teams') ?? array();
        foreach($teams as $team){
            DB::table('teams')->updateOrInsert(["team_name" => $team]);
            #handle faliure
        }
        return response('OK', 200);
    }

    private function has_min_team_amount(){
        return count($this->get_teams_by_id()) >= 4;
    }
    
    public function create_games_table(){
        if (!$this->has_min_team_amount()){
            return response("Must have at least 4 teams", 400);
        }
        if (Schema::hasTable('games')) {
            return response("Table already exists", 200);
        }
        return Schema::create('games', function(Blueprint $table){
            $table->increments('game_id');
            $table->tinyInteger('round');
            $table->tinyInteger('week');
            $table->integer('home_group_id');
            $table->integer('away_group_id');
            $table->tinyInteger('home_score')->nullable();
            $table->tinyInteger('away_score')->nullable();
            $table->boolean('is_done')->virtualAs("home_score IS NOT NULL AND away_score IS NOT NULL");
        });
    }
    

}
