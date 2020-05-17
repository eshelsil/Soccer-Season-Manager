<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Game;
use App\Team;

class DefaultRouteController extends Controller
{
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
}