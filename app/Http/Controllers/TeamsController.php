<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


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

    public function index(){
        return view('set_teams');
    }
}
