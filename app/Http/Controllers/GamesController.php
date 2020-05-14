<?php

namespace App\Http\Controllers;

class GamesController extends Controller
{
    public function index()
    {
        return view('games',[
            'teams_by_id' => app('RegisteredTeamsManager')->get_teams_by_id()
        ]);
    }
}