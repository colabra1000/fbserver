<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use App\AllFixture;
use App\Table;

class Team extends Model
{


    private $t;

    public function competition(){
        return $this->belongsTo('App\Competition');
    }

    public function standing(){
        return $this->hasOne('App\Table');
    }

    public function previousMatch(){
       
        
        // $matchDay =  $this->competition->currentMatchDay - 1;
        $currentDay = $this->standing->playedGames;
        return AllFixture::where('match_day', $currentDay)->where('competition_id', $this->competition->id)->where(function($query){
            $query->where('homeTeam_id', $this->id)->orWhere('awayTeam_id', $this->id);
        })->first();
      
    }

    

    public function nextMatch(){
    
        // $matchDay =  $this->competition->currentMatchDay;
        $currentDay = $this->standing->playedGames + 1;
        return AllFixture::where('match_day', $currentDay)->where('competition_id', $this->competition->id)->where(function($query){
            $query->where('homeTeam_id', $this->id)->orWhere('awayTeam_id', $this->id);
        })->first();
    }
}
