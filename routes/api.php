<?php

use Illuminate\Http\Request;

use App\Competition;
use App\Table;
use App\Test;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getTables', 'FbController@getTables');

Route::get('/getCompetitionsFixtures', 'FbController@getCompetitionsFixtures');

Route::get('/getLeagueTeams', 'FbController@getLeagueTeams');

Route::get('/getMatche', 'FbController@testStuffs');

Route::get('/leagueTeam', function(Request $request){
    return response()->json($datum)
    ->header('Content-Type', 'application/json');
});
