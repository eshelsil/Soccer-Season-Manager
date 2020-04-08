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

Route::get('/', function () {
    return view('welcome');
});
Route::get('groups', function () {
    return view('groups');
});
Route::get('games', 'GamesController@index');
Route::get('games/{team_id}', 'GamesController@team');
Route::get('table', function () {
    return view('table');
});
