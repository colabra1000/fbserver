<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllFixture extends Model
{
    protected $table = 'all_fixtures';

    public function homeTeam(){
        return $this->belongsTo('App\Team', 'homeTeam_id');
    }

    public function awayTeam(){
        return $this->belongsTo('App\Team', 'awayTeam_id');
    }

    // public function match(){
    //     return $this->belongsTo('App\', 'match_id');
    // }

    public function competition(){
        return $this->belongsTo('App\Competition', 'competition_id');
    }

}
