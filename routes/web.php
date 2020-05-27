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

Route::delete('/api/users/{id}', 'UsersAPIController@destroy');
Route::put('/api/users/{id}/set_admin', 'UsersAPIController@set_admin');
Route::put('/api/users/{id}/set_regular', 'UsersAPIController@set_regular');
Route::get('/api/users', 'UsersAPIController@index');

Route::get('/', 'ContentController@index');
Route::get('games', 'ContentController@season_games');
Route::get('table', 'ContentController@season_table');

Route::middleware('admin')->group(function () {
    Route::get('/admin', 'ContentController@admin_index');
    Route::get('admin/schedule', 'ContentController@games_scheduler');
    Route::get('admin/teams', 'ContentController@set_teams');
    Route::get('admin/scores', 'ContentController@set_scores');
    Route::get('admin/users', 'ContentController@manage_users');
});




Route::get('login', ['as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('login', ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::post('logout', ['as' => 'logout', 'uses' => 'Auth\LoginController@logout']);

Route::get('register', ['as' => 'register', 'uses' => 'Auth\RegisterController@showRegistrationForm']);
Route::post('register', ['as' => 'register.post', 'uses' => 'Auth\RegisterController@register']);
