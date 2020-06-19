<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\AllFixtures;

class AllFixture extends Model
{
    protected $table = 'all_fixtures';

    public function homeTeam(){
        return $this->belongsTo('App\Team', 'homeTeam_id');
    }

    public function awayTeam(){
        return $this->belongsTo('App\Team', 'awayTeam_id');
    }

    public function competition(){
        return $this->belongsTo('App\Competition', 'competition_id');
    }

    public function currentMatchDay(){
         
         $matchDay = AllFixture::where('competition_id', $this->competition_id)->where('status', 'scheduled')->orderBy('match_day')->limit(1)->first();
         return $matchDay->match_day ;  
    }

}
