<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Team;
use Log;
use Exception;

class TeamsAPIController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = [];
        $teams = Team::query()->get();
        foreach($teams as $team){
            array_push($res, $team->json_export());
        }
        #NOTE return as array or object? this way or like GamesAPIController implementation?
        return response()->json($res, 200);
    }

    /**
     * Store newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $teams_array = $request->input('teams', []);
        return DB::transaction(function () use($teams_array) {
            try{
                $output = [];
                foreach($teams_array as $index=>$team ) {
                    $res = $this->store_single_team($team['name']);
                    array_push($output, $res);
                }
            } catch (Exception $e ) {
                return response($e->getMessage(), 400);
            }
            return response()->json($output, 200);
        });
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $name
     * @return array #NOTE is this the correct way?
     */
    private function store_single_team($name)
    {
        $name_exists = Team::query()->where('team_name', $name)->exists();
        if ( $name_exists ){
            $error_str = "The name \"$name\" is already used by another team";
            throw new Exception($error_str);
            // return response("Home team is already hosting away team this season", 400);
        }
        $team = new Team;
        $team->team_name = $name;
        $team->save();
        return $team->json_export();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $team = Team::find($id);
        $team->delete();
        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function reset_all()
    {
        Team::query()->delete();
        return response()->json([], 200);
    }
}
