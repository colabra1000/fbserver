<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;

class TeamGetController extends Controller
{
    public function getTeam($id){
        return Team::get($id);
    }
}
