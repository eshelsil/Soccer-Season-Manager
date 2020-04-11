<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Game extends Model
{
    protected $table = 'games'; //(unnecessary -> auto generated)
    public function isDraw(){
        return $this->home_score == $this->away_score;
    }
}
