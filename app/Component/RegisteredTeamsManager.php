<?php

namespace App\Component;

use App\Game;
use App\Team;

class RegisteredTeamsManager {
    
    private $teams_by_id = null;

    private function fetch_teams_by_id(){
        $teams = Team::query()->get();
            $teams_by_id = array();
        foreach($teams as $team_data){
            $teams_by_id[$team_data->team_id] = $team_data->team_name;
        };
        return $teams_by_id;
    }

    public function get_teams_by_id($refresh=false){
        if ($refresh || is_null($this->teams_by_id)){
            $this->teams_by_id = $this->fetch_teams_by_id();
        }
        return $this->teams_by_id;
    }

    public function get_teams_count(){
        return count($this->get_teams_by_id());
    }

    public function get_weeks_per_round_count(){
        return $this->get_teams_count() -1;
    }

    public function get_weeks_count(){
        return $this->get_weeks_per_round_count() * 2;
    }

    public function get_round_of_week($week){
        return ceil($week / $this->get_weeks_per_round_count());
    }

    public function has_min_teams_amount(){
        return $this->get_teams_count() >= 4;
    }

    public function is_teams_count_even(){
        return $this->get_teams_count() % 2 == 0;
    }

    public function is_all_games_scheduled(){
        $teams_count = $this->get_teams_count();
        $games_count = Game::query()->count();
        return $games_count >= $teams_count * ( $teams_count - 1 );
    }

    public function is_locked(){
        return Game::query()->exists();
    }



}