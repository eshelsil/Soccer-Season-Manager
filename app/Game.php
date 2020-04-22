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

    public function getTeamSide($team_id){
        switch($team_id){
            case $this->home_team_id:
                return 'home';
            case $this->away_team_id:
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

}
