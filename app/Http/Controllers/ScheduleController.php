<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class ScheduleController extends Controller
{
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

    public function index(Request $request){
        $regist_manager = app('RegisterationManager');
        return view('scheduling', [
            'weeks_count' => $regist_manager->get_weeks_count(),
            'teams_by_id' => $regist_manager->get_teams_by_id(),
            'can_schedule_games' => $regist_manager->can_start_scheduling(),
            'games_in_season' => $regist_manager->get_games_in_season_count()
        ]);
    }
}
