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
Route::put('/api/games', 'GamesAPIController@update_many');

Route::delete('/api/teams/reset_all', 'TeamsAPIController@reset_all');
Route::resource('/api/teams', 'TeamsAPIController');


Route::get('/', 'ContentController@index');
Route::get('games', 'ContentController@season_games');
Route::get('table', 'ContentController@season_table');
Route::get('/admin', 'ContentController@admin_index');
Route::get('admin/schedule', 'ContentController@games_scheduler');
Route::get('admin/teams', 'ContentController@set_teams');
Route::get('admin/scores', 'ContentController@set_scores');




