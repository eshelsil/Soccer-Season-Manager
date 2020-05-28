<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\SeasonTableUtil;
use App\Game;
use App\Team;

class ContentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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

    private function is_admin(){
        return Auth::user()->isAdmin();
    }

    public function index()
    {
        if (!$this->is_admin() || Game::where('is_done', 1)->exists()){
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

    public function season_table(Request $request){
        $query_until_week = $request->query('week');
        $games = Game::query()
            ->where('is_done', 1)
            ->get();
        $last_week = $games->max('week');
        $has_games = !is_null($last_week);
        if (!is_null($query_until_week)){
            $games = $games->filter(function ($game) use($query_until_week) {
                return $game['week'] <= $query_until_week;
            });
        }
        
        $teams_by_id = app('RegisterationManager')->get_teams_by_id();
        $table_rows = SeasonTableUtil::get_table($games, array_keys($teams_by_id));
        foreach($table_rows as $index => $row){
            $row['name'] = $teams_by_id[$row['id']];
        }

        return view('season_table', [
            'has_games' => $has_games,
            'last_week' => $last_week,
            'has_teams' => count($teams_by_id) > 0,
            'table_rows' => $table_rows,
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

    public function manage_users(){
        return view('users');
    }

}