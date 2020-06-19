<?php

use Illuminate\Http\Request;

use App\Competition;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Http\Resources\Scorer as ScorerResource;
use App\Scorer;

use App\Http\Resources\Table as TableResource;
use App\Table;

use App\Http\Resources\Team as TeamResource;
use App\Team;

use App\Http\Resources\AllFixture as AllFixtureResource;
use App\AllFixture;

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


Route::get('/fixtures/{matchDay}/{competitionId}', function($matchDay, $competitionId){


    // $matchDay = AllFixture::where('competition_id', $competitionId)->first()->matchDay();
    return AllFixtureResource::collection(Allfixture::where('competition_id', $competitionId)->where('match_day', $matchDay)->orderBy('utcDate')->get());
   

});


Route::get('/scorers/{competitionId}', function($competitionId){
               
    return ScorerResource::collection(Scorer::where('competition_id', $competitionId)->get());
});

Route::get('/tables/{competitionId}', function($competitionId){
    return TableResource::collection(Table::where('competition_id', $competitionId)->get());
});

Route::get('/teams/{competitionId}', function($competitionId){
    return TeamResource::collection(Team::where('competition_id', $competitionId)->get());
});

// Route::get('/allfixtures/{competitionId}', function($competitionId){
//     return AllFixtureResource::collection(Allfixture::where('competition_id', $competitionId)->get());
// });

// Route::get('/fixtures/{matchDay}/{competitionId}', function($competitionId, $matchDay){

//     // $matchDay = AllFixture::where('competition_id', $competitionId)->first()->matchDay();
//     return AllFixtureResource::collection(Allfixture::where('competition_id', $competitionId)->where('match_day', $matchDay)->orderBy('utcDate')->get());

// });

Route::get('/todayfixtures/{competitionId}', function($competitionId){

    // $currentMatchDay = AllFixture::where('competition_id', $competitionId)->first()->currentMatchDay();
    $currentMatchDay = Competition::where('id', $competitionId)->first()->currentMatchDay;
    return AllFixtureResource::collection(Allfixture::where('competition_id', $competitionId)->where('match_day', $currentMatchDay)->orderBy('utcDate')->get());

});

Route::get("/allfixtureslive", function(){

    return AllFixtureResource::collection(Allfixture::where('status', 'live')->orWhere('status', 'live')->orderBy('competition_id')->orderBy('utcDate')->get());

});

Route::get('/test/{id}', function($id){
    return new TeamResource(Team::find($id));
});



//Route for getTeam contains so  much about the team.

