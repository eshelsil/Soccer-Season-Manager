<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Game;
use App\Team;
use Log;

class GamesAPIController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query_team_id = $request->query('team_id');
        $query_round = $request->query('round');
        $query_week = $request->query('week');
        $query_is_done = $request->query('is_done');
    
        $games = Game::query()
            ->where(function($query) use($query_week, $query_round, $query_is_done) {
                if (!is_null($query_week)){
                    $query->where('week', $query_week);
                }
                if (!is_null($query_round)){
                    $query->where('round', $query_round);
                }
                if (!is_null($query_is_done)){
                    $query->where('is_done', $query_is_done);
                }
            })
            ->where(function($query) use($query_team_id) {
                if (!is_null($query_team_id)){
                    $query->where('home_team_id', $query_team_id)
                        ->orWhere('away_team_id', $query_team_id);
                }
            })
            ->get();

        $res=array();
        foreach($games as $game){
            $output = $game->json_export();
            $output['team_side'] = $game->getTeamSide($query_team_id);
            $output['team_result'] = $game->getTeamResult($query_team_id);
            $res[$game->game_id] = $output;
        }
        return Response($res, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $teams_manager = app('RegisteredTeamsManager');
        $week = $request->input('week');
        $home_team_id = $request->input('home_team_id');
        $away_team_id = $request->input('away_team_id');
        
        #NOTE move validation to another resource/function;
        if (is_null($week)){
            return response("Must pass a valid \"week\" parameter", 400);
        }
        if (is_null($home_team_id)){
            return response("must pass a valid home_team_id parameter", 400);
        }
        if (is_null($away_team_id)){
            return response("must pass a valid away_team_id parameter", 400);
        }
        $round = $teams_manager->get_round_of_week($week);
        
        #NOTE move validation to another resource/function;
        if (!$teams_manager->has_min_teams_amount()){
            return response("In order to schedule a game there must be at least 4 teams", 400);
        }
        
        if (!$teams_manager->is_teams_count_even()){
            return response("In order to schedule a game there must be an even number of teams", 400);
        }

        $teams_playing = [$home_team_id, $away_team_id];


        if ($away_team_id == $home_team_id){
            return response("Team cannot play against itself", 400);
        }

        $week_not_team_unique = Game::query()
                ->where('week', $week)
                ->where(function($query) use($teams_playing){
                    $query->whereIn('home_team_id', $teams_playing)
                        ->orWhere(function($query) use($teams_playing){
                            $query->whereIn('away_team_id', $teams_playing);
                        });
                })
                ->exists();
        if ( $week_not_team_unique ){
            return response("One of the teams is already playing this week", 400);
        }

        $round_not_teams_unique = Game::query()
                ->where('round', $round)
                ->whereIn('home_team_id', $teams_playing)
                ->whereIn('away_team_id', $teams_playing)
                ->exists();
        if ( $round_not_teams_unique ){
            return response("Teams already play against each other on this round", 400);
        }

        $not_home_away_unique = Game::query()
                ->where('home_team_id', $home_team_id)
                ->where('away_team_id', $away_team_id)
                ->exists();
        if ( $not_home_away_unique ){
            return response("Home team is already hosting away team this season", 400);
        }
        $game = new Game;
        $game->round = $round;
        $game->week = $week;
        $game->home_team_id = $home_team_id;
        $game->away_team_id = $away_team_id;
        $game->save();
        return response($game->json_export(), 200);
        // return Game::insert([
        //     'round' => $round,
        //     'week' => $week,
        //     'home_team_id' => $home_team_id,
        //     'away_team_id' => $away_team_id
        // ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update Many resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_many(Request $request)
    {
        $games_data = $request->input('games');
        $output = [];
        return DB::transaction(function () use($games_data, $output) {
            foreach($games_data as $index=>$game_data ) {
                $game = Game::find($game_data['id']);
                $game->update(
                    ['home_score' => $game_data['home'], 'away_score' => $game_data['away']]
                );
                $output[$game->game_id] = $game->json_export();
            }
            return response()->json($output, 200);
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $home_score = $request->input('home');
        $away_score = $request->input('away');
        $game = Game::find($id);
        $game->update(
            ['home_score' => $home_score, 'away_score' => $away_score]
        );
        return response()->json($game->json_export(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game = Game::find($id);
        $game->delete();
        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset_all()
    {
        Game::query()->delete();
        return response()->json([], 200);
    }
}