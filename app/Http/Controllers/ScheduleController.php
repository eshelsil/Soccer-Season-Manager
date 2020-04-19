<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


#NOTE bad manager implementation -> split to relevant controllers: 'teams_setter', 'games_setter', 'app_main_router'

class ScheduleController extends Controller
{
    private $teams_by_id = null;

    public function __construct()
    {
        $this->ensure_games_table_existance();
    }

    private function ensure_games_table_existance(){
        if (!Schema::hasTable('games')) {
            Schema::create('games', function(Blueprint $table){
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
    }

    public function get_teams_by_id(){
        if (is_null($this->teams_by_id)){
            $teams = DB::table('teams')->get();
            $teams_by_id = array();
            foreach($teams as $team_data){
                $teams_by_id[$team_data->team_id] = $team_data->team_name;
            };
            $this->teams_by_id = $teams_by_id;
        }
        return $this->teams_by_id;
    }

    public function get_teams_count(){
        return count($this->get_teams_by_id());
    }

    public function get_games_per_week(){
        return $this->get_teams_count() / 2;
    }

    public function get_weeks_count(){
        $teams_count = $this->get_teams_count();
        return ($teams_count - 1) * 2;
    }

    private function has_min_team_amount(){
        return $this->get_teams_count() >= 4;
    }

    private function is_teams_count_even(){
        return $this->get_teams_count() % 2 == 0;
    }

    public function is_all_games_scheduled(){
        $teams_count = $this->get_teams_count();
        $games_count = DB::table('games')->count();
        return $games_count >= $teams_count * ( $teams_count - 1 );
    }

    public function index(Request $request){
        $selected_week = $request->query('set_week');
        $round = $request->query('round');
        $week = $request->query('week');
        $team_id = $request->query('team_id');
        $has_available_games = DB::table('games')->exists();
        
        if ($has_available_games){
            $filtered_games = DB::table('games')
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
            $filtered_games = null;
        }
        
        $teams_by_id = $this->get_teams_by_id();
        $teams_count = $this->get_teams_count();
        $games_per_week = $this->get_games_per_week();
        $weeks_count = $this->get_weeks_count();
        $weeks_to_schedule = range(1, $weeks_count);
        $full_weeks = DB::table('games')->select('week', DB::raw('count(*) as games_count'))->groupBy('week')->get();
        foreach($full_weeks as $week_data){
            if ($week_data->games_count >= $games_per_week){
                $weeks_to_schedule = array_diff($weeks_to_schedule, [$week_data->week]);
            }
        }
        if (!in_array($selected_week, $weeks_to_schedule) && !empty($weeks_to_schedule)){
            foreach (array_values($weeks_to_schedule) as $available_week){
                if ($available_week > $selected_week){
                    $selected_week = $available_week;
                    break;
                }
            }
            if (!in_array($selected_week, $weeks_to_schedule)){
                $selected_week = array_values($weeks_to_schedule)[0];
            }
        }

        $games = DB::table('games')->get();
        // $games_on_selected_week = DB::table('games')->where('week', $selected_week)->get();
        $games_on_selected_week = $games->filter(function($game) use($selected_week){
            return $game->week == $selected_week;
        });
        $available_teams = array_keys($teams_by_id);
        foreach($games_on_selected_week as $game){
            $available_teams = array_diff($available_teams, [$game->home_team_id, $game->away_team_id]);
        }

        return view('scheduling', [
            'query_params' => array(
                'set_week'=>$selected_week,
                'round'=>$round,
                'week'=>$week,
                'team_id'=>$team_id,
            ),
            'weeks_to_schedule' => $weeks_to_schedule,
            'available_teams' => $available_teams,
            'weeks_count' => $weeks_count,
            'teams_by_id' => $teams_by_id,
            'filtered_games' => $filtered_games,
            'has_available_games' => $has_available_games,
            'allow_set_scores' => $this->is_all_games_scheduled()
        ]);
    }

    public static function truncate_games_table(){
        return DB::table('games')->truncate();
    }
    
    public function delete_game($game_id){
        return DB::table('games')->where('game_id', $game_id)->delete();
    }
    
    public function schedule_game(Request $request){
        $params = $request->input();
        $week = $params['week'];
        $home_team_id = $params['home_team_id'];
        $away_team_id = $params['away_team_id'];
        $teams_count = $this->get_teams_count();
        $weeks_per_round = $teams_count - 1;
        $round = ceil($week / $weeks_per_round);
        return $this->add_game_to_db($round, $week, $home_team_id, $away_team_id);
    }

    
    private function add_game_to_db($round, $week, $home_team_id, $away_team_id){
        #NOTE move validation to another resource/function
        if (!$this->has_min_team_amount()){
            return response("In order to schedule a game there must be at least 4 teams", 400);
        }
        
        if (!$this->is_teams_count_even()){
            return response("In order to schedule a game there must be an even number of teams", 400);
        }

        $teams_playing = [$home_team_id, $away_team_id];


        if ($away_team_id == $home_team_id){
            return response("Team cannot play against itself", 400);
        }

        $week_not_team_unique = DB::table('games')
                ->where('week', $week)
                ->where(function($query) use($teams_playing){
                    $query->whereIn('home_team_id', $teams_playing)
                        ->orWhere(function($query) use($teams_playing){
                            $query->whereIn('away_team_id', $teams_playing);
                        });
                })
                ->exists();
        if ( $week_not_team_unique ){
            return response("One of the teams is already playing this week", 400);
        }

        $round_not_teams_unique = DB::table('games')
                ->where('round', $round)
                ->whereIn('home_team_id', $teams_playing)
                ->whereIn('away_team_id', $teams_playing)
                ->exists();
        if ( $round_not_teams_unique ){
            return response("Teams already play against each other on this round", 400);
        }

        $not_home_away_unique = DB::table('games')
                ->where('home_team_id', $home_team_id)
                ->where('away_team_id', $away_team_id)
                ->exists();
        if ( $not_home_away_unique ){
            return response("Home team is already hosting away team this season", 400);
        }

        return DB::table('games')->insert([
            'round' => $round,
            'week' => $week,
            'home_team_id' => $home_team_id,
            'away_team_id' => $away_team_id
        ]);
    }
    
    public function auto_schedule_games(){
        $games_count = DB::table('games')->count();
        if ($games_count > 0){
            return response("\"games\" table must be empty in order to auto schedule games", 400);
        }
        $games = $this->generate_games();
        return DB::transaction(function () use($games) {
            foreach($games as $game){    
                $this->add_game_to_db($game['round'],$game['week'],$game['home_team_id'], $game['away_team_id']);
            }
        });
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
        #NOTE is this the right place to do this? would it be better to generate games in javascipt and pass to php?
        #Is this one of the purposes of PHP or should it use this kind of functionality only for objects that are not accessible by frontend?
        #--> this should be done on frontend, no reason to waste server resources for that (in general) --> note that if this functionallity is moved to FE, validation should take place 

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
