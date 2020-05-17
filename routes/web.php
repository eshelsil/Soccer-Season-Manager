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


Route::get('/', 'DefaultRouteController@index');
Route::get('/admin', 'DefaultRouteController@admin_index');
Route::get('admin/schedule', 'ScheduleController@index');
Route::get('admin/teams', 'TeamsController@index');

Route::get('admin/scores', 'ScoresController@index');

Route::get('games', 'GamesController@index');


Route::get('table', 'TableController@index');
