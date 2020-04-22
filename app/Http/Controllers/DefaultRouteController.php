<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Game;
use App\Team;

class DefaultRouteController extends Controller
{
    public function index()
    { 
        if (!Schema::hasTable('games') || !Game::query()->exists()){
            return redirect('/set_teams');
        }
        $teams_count = Team::query()->count();
        $games_per_season = $teams_count * ($teams_count - 1);
        if (Game::query()->count() < $games_per_season){
            return redirect('/schedule');
        }
        if (Game::where('is_done', 0)->exists()){
            return redirect('/set_scores');
        }
        return redirect('/table');
    }
}