<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//#NOTE how to choose urls properly? (REST etc.)
//#NOTE how to choose post/delete/put methods properly? (REST etc.)

Route::delete('/api/games/reset_all', 'GamesAPIController@reset_all');
Route::resource('/api/games', 'GamesAPIController');
Route::patch('/api/games', 'GamesAPIController@update_many');

Route::delete('/api/teams/reset_all', 'TeamsAPIController@reset_all');
Route::resource('/api/teams', 'TeamsAPIController');


Route::get('/', 'DefaultRouteController@index');
Route::get('schedule', 'ScheduleController@index');
Route::get('set_teams', 'TeamsController@index');
Route::post('set_teams', 'TeamsController@set_teams');
Route::delete('set_teams/delete_all', 'TeamsController@truncate_teams_table');
Route::post('teams/delete/{team_id}', 'TeamsController@delete_team');
Route::post('teams/add', 'TeamsController@add_team');
Route::post('schedule/auto', 'ScheduleController@auto_schedule_games');
Route::post('schedule/add_game', 'ScheduleController@schedule_game');
Route::post('schedule/delete_game/{game_id}', 'ScheduleController@delete_game');
Route::post('schedule/reset_games', 'ScheduleController@truncate_games_table');

Route::get('set_scores', 'ScoresController@index');
Route::post('set_scores/randomize', 'ScoresController@randomize_game_scores');
Route::post('set_scores/reset', 'ScoresController@reset_all');
Route::post('set_scores/delete/{game_id}', 'ScoresController@reset_score');
Route::post('set_scores/update/{game_id}', 'ScoresController@update_score');

Route::get('reset_options', 'ResetController@index');

// Route::resource('reset_options', 'ResetController@index');


Route::get('games', 'GamesController@index');
// Route::get('games/{team_id}', 'GamesController@team');


Route::get('table', 'TableController@index');
// Route::get('table', function () {
//     return view('table');
// });
