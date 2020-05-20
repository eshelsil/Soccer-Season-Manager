<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Game;
use App\Team;

class ContentController extends Controller
{

    public function __construct()
    {
        $this->ensure_teams_table_existance();
        $this->ensure_games_table_existance();
    }

    private function ensure_teams_table_existance(){
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function($table){
                $table->increments('team_id');
                $table->string('team_name', 50)->unique();
            });
        }
    }

    private function ensure_games_table_existance(){
        if (!Schema::hasTable('games')) {
            Schema::create('games', function($table){
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

    public function admin_index()
    { 
        if (!Schema::hasTable('games') || !Game::query()->exists()){
            return redirect('/admin/teams');
        }
        $teams_count = Team::query()->count();
        $games_per_season = $teams_count * ($teams_count - 1);
        if (Game::query()->count() < $games_per_season){
            return redirect('/admin/schedule');
        }
        return redirect('/admin/scores');
    }

    public function index()
    {
        if (Game::where('is_done', 1)->exists()){
            return redirect('/table');
        }
        return redirect('/admin');
    }

    public function season_games()
    {
        return view('games',[
            'teams_by_id' => app('RegisterationManager')->get_teams_by_id(),
            'has_games' => Game::exists()
        ]);
    }

    public function season_table(){
        $games = Game::where('is_done', 1)->get();
        return view('table', [
            'games' => $games,
            'teams_by_id' => app('RegisterationManager')->get_teams_by_id()
        ]);
    }

    public function set_scores(Request $request)
    {
        $is_done = $request->query('is_done') == 1;
        $has_available_games = Game::query()->where('is_done', $is_done)->exists();
        $regist_manager = app('RegisterationManager');
        return view('set_scores', [
            'is_on_done_tab'=>$is_done,
            'has_available_games' => $has_available_games,
            'teams_by_id' => $regist_manager->get_teams_by_id(),
        ]);
    }

    public function games_scheduler(Request $request){
        $regist_manager = app('RegisterationManager');
        return view('scheduling', [
            'weeks_count' => $regist_manager->get_weeks_count(),
            'teams_by_id' => $regist_manager->get_teams_by_id(),
            'can_schedule_games' => $regist_manager->can_start_scheduling(),
            'games_in_season' => $regist_manager->get_games_in_season_count()
        ]);
    }

    public function set_teams(){
        return view('set_teams');
    }

}