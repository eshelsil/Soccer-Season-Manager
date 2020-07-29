<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Game extends Model
{
    protected $primaryKey = 'game_id';
    protected $fillable = ['home_score', 'away_score'];
    public $timestamps = false;
    public function isDraw(){
        return $this->isDone() && $this->home_score == $this->away_score;
    }

    public function isDone(){
        return $this->is_done;
    }

    public function isHomeWinner(){
        return $this->home_score > $this->away_score;
    }

    public function isAwayWinner(){
        return $this->home_score < $this->away_score;
    }

    public function getHomeTeamId(){
        return $this->home_team_id;
    }

    public function getAwayTeamId(){
        return $this->away_team_id;
    }

    public function getTeamSide($team_id){
        switch($team_id){
            case $this->getHomeTeamId():
                return 'home';
            case $this->getAwayTeamId():
                return 'away';
            default:
                return null;
        }
    }

    public function getWinnerSide(){
        if (!$this->isDone()){
            return null;
        }
        if ($this->isHomeWinner()){
            return 'home';
        }
        if ($this->isAwayWinner()){
            return 'away';
        }
        if ($this->isDraw()){
            return 'draw';
        }
        return null;
    }

    public function getTeamResult($team_id){
        $team_side = $this->getTeamSide($team_id);
        $winner_side = $this->getWinnerSide();
        if (is_null($winner_side) || is_null($team_side)){
            return null;
        }
        if ($winner_side == 'draw'){
            return 'draw';
        }
        if ($winner_side == $team_side){
            return 'win';
        }
        return 'lose';
    }

    
    public function json_export(){
        $teams_by_id = app('RegisterationManager')->get_teams_by_id();
        return  [
            'id' => $this->game_id,
            'round' => $this->round,
            'week' => $this->week,
            'time' => $this->time,
            'home_team_id' => $this->getHomeTeamId(),
            'home_team_name' => $teams_by_id[$this->getHomeTeamId()],
            'away_team_id' => $this->getAwayTeamId(),
            'away_team_name' => $teams_by_id[$this->getAwayTeamId()],
            'home_score' => is_numeric($this->home_score) ? $this->home_score * 1 : null,
            'away_score' => is_numeric($this->away_score) ? $this->away_score * 1 : null,
            'is_done' => $this->is_done,
            'is_home_winner' => $this->isHomeWinner(),
            'is_away_winner' => $this->isAwayWinner()
        ];
    }


}
