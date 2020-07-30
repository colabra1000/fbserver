<?php

use App\Scorer;
use App\Team;
use App\Http\Controllers\FbController;
use App\Http\Resources\Team as TeamResource;
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

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/team/{id}', function ($id) {
    
    return TeamResource::collection(Team::find($id));
});

Route::get('/test1', function(){
    // $fbController = new FbController;
    // return $fbController->testo();
    // return 
});

