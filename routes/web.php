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

Route::get('/', 'ManageController@home');
Route::get('manage', 'ManageController@index');
Route::post('manage/init_teams', 'ManageController@create_teams_table');
Route::post('manage/init_games', 'ManageController@create_games_table');
Route::post('manage/add_teams', 'ManageController@add_teams')->name('add_teams');
Route::delete('manage/drop_teams', 'ManageController@drop_teams_table');

Route::get('games', 'GamesController@index');
// Route::get('games/{team_id}', 'GamesController@team');


Route::get('table', 'TableController@index');
// Route::get('table', function () {
//     return view('table');
// });
