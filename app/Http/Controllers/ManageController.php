<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ManageController extends Controller
{
    private function is_all_games_scheduled(){
        $teams_count = DB::table('teams')->count();
        $games_count = DB::table('games')->count();
        return $games_count >= $teams_count * ( $teams_count - 1 );
    }

    public static function get_teams_by_id(){
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
        $teams_table = Schema::hasTable('teams');    
        if($games_table && $teams_table){
            if ($this->is_all_games_scheduled()){
                    return redirect('set_scores');
            }
        }
        return redirect('manage');
    }

    public function index(Request $request){
        $games_table_exists = Schema::hasTable('games');
        $teams_table_exists = Schema::hasTable('teams');
        if (!$games_table_exists || !$teams_table_exists){
            return $this->show_set_teams();
        }
        return $this->show_scheduling();
    }

    public function show_set_teams(){
        $this->create_teams_table();
        return view('set_teams', ['teams_by_id' => $this->get_teams_by_id()]);
    }

    public function show_scheduling(){
        $this->create_games_table();
        $games = DB::table('games')->get();
        return view('scheduling', [
            'teams_by_id' => $this->get_teams_by_id(),
            'games' => $games,
            'allow_set_scores' => $this->is_all_games_scheduled()
        ]);
    }


    
    public static function create_teams_table(){
        if (Schema::hasTable('teams')) {
            return "Table already exists";
        }
        return Schema::create('teams', function(Blueprint $table){
            $table->increments('team_id');
            $table->string('team_name', 50)->unique();
        });
        
    }
    
    public static function drop_teams_table(){
        if (!Schema::hasTable('teams')) {
            return "\"teams\" table does not exist";
        }
        return Schema::drop('teams');
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
            $table->integer('home_team_id');
            $table->integer('away_team_id');
            $table->tinyInteger('home_score')->nullable();
            $table->tinyInteger('away_score')->nullable();
            $table->boolean('is_done')->virtualAs("home_score IS NOT NULL AND away_score IS NOT NULL");
        });
    }
    
    
    public static function drop_games_table(){
        if (!Schema::hasTable('games')) {
            return "\"games\" table does not exist";
        }
        return Schema::drop('games');
    }
    
    public function auto_schedule_games(){
        # handle no teams_table
        $games_count = DB::table('games')->count();
        if ($games_count > 0){
            return response("\"games\" table must be empty in order to auto schedule games", 400);
        }
        $games = $this->generate_games();
        foreach($games as $game){
            DB::table('games')->insert(
                ['round' => $game['round'],
                'week' => $game['week'],
                'home_team_id' => $game['home_team_id'],
                'away_team_id' => $game['away_team_id']]
            );
        }
        return response(200);
        
    }

    private function generate_games(){
        $team_ids = array_keys($this->get_teams_by_id());
        $first_round_games = $this->generate_first_round_order($team_ids);
        $last_week = end($first_round_games)["week"];
        $second_round_games = array();
        foreach($first_round_games as $game){
            array_push($second_round_games, array(
                "round"=> 2,
                "week"=> $last_week + $game["week"],
                "home_team_id"=> $game["away_team_id"],
                "away_team_id"=> $game["home_team_id"]
            ));
        }
        return array_merge($first_round_games, $second_round_games);
    }

    private function generate_first_round_order($ids){
        # explanation on method -> https://nrich.maths.org/1443
        shuffle($ids);
        $middle_of_poligon = $ids[0];
        array_splice($ids, 0, 1);
        $connections = array();
        $weeks_count = count($ids);
        $lower_index = 0;
        $higher_index = $weeks_count -1;
        $is_higher_index_hosting = TRUE;
        while ($higher_index >= $lower_index){
            if ($higher_index == $lower_index){
                $connections["middle"] = $higher_index;
                break;
            }
            if ($is_higher_index_hosting){
                $connections[$higher_index] = $lower_index;
            }else {
                $connections[$lower_index] = $higher_index;
            }
            $lower_index ++;
            $higher_index --;
            $is_higher_index_hosting = !$is_higher_index_hosting;
        }
        $games = array();
        foreach(range(1, $weeks_count) as $week){
            $last_id = array_pop($ids);
            array_unshift($ids, $last_id);
            
            foreach($connections as $ploygon_pos_a => $polygon_pos_b){
                if ($ploygon_pos_a == "middle"){
                    $teams = array($middle_of_poligon, $ids[$polygon_pos_b]);
                    $home_team_id = $teams[$week % 2];
                    $away_team_id = $teams[($week + 1) % 2];
                } else {
                    $home_team_id = $ids[$ploygon_pos_a];
                    $away_team_id = $ids[$polygon_pos_b];
                }
                array_push($games, array(
                    "round"=>1,
                    "week"=>$week,
                    "home_team_id"=>$home_team_id,
                    "away_team_id"=>$away_team_id
                ));
            }
        }
        return $games;
    }
}
