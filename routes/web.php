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

Route::delete('/api/games/reset_all', 'GamesAPIController@reset_all');
Route::resource('/api/games', 'GamesAPIController');
Route::patch('/api/games', 'GamesAPIController@update_many');

Route::delete('/api/teams/reset_all', 'TeamsAPIController@reset_all');
Route::resource('/api/teams', 'TeamsAPIController');


Route::get('/', 'DefaultRouteController@index');
Route::get('schedule', 'ScheduleController@index');
Route::get('set_teams', 'TeamsController@index');

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
