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
Route::get('/', 'ManageController@home');
Route::get('manage', 'ManageController@index');
Route::post('manage/init_teams', 'ManageController@create_teams_table');
Route::post('manage/init_games', 'ManageController@create_games_table');
Route::post('manage/set_teams', 'ManageController@set_teams')->name('set_teams');
Route::delete('manage/drop_teams', 'ManageController@drop_teams_table');
Route::delete('manage/drop_games', 'ManageController@drop_games_table');
Route::post('manage/auto_schedule', 'ManageController@auto_schedule_games');
Route::post('manage/schedule_game', 'ManageController@schedule_game');
Route::post('manage/delete_game/{game_id}', 'ManageController@delete_game');
Route::post('manage/randomize_scores', 'ManageController@randomize_game_scores');
Route::post('manage/reset_games', 'ManageController@truncate_games_table');

Route::get('set_scores', 'ScoresController@index');
Route::post('set_scores/randomize', 'ScoresController@randomize_game_scores');
Route::post('set_scores/reset', 'ScoresController@reset_all');
Route::post('set_scores/delete/{game_id}', 'ScoresController@reset_score');
Route::post('set_scores/update/{game_id}', 'ScoresController@update_score');
Route::post('teams/delete/{team_id}', 'ManageController@delete_team');
Route::post('teams/add', 'ManageController@add_team');

Route::get('reset_options', 'ResetController@index');

// Route::resource('reset_options', 'ResetController@index');


Route::get('games', 'GamesController@index');
// Route::get('games/{team_id}', 'GamesController@team');


Route::get('table', 'TableController@index');
// Route::get('table', function () {
//     return view('table');
// });
