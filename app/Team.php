<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Team extends Model
{
    protected $primaryKey = 'team_id';
    public $timestamps = false;

    public function json_export(){
        return  [
            'id' => $this->team_id,
            'name' => $this->team_name
        ];
    }

}
