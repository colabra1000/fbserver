<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class Table extends Model
{
    protected $fillable = ['id',
                            'position',
                            'playedGames',
                            'won',
                            'draw',
                            'lost',
                            'points',
                            'goalsFor',
                            'goalsAgainst',
                            'goalDifference',
                            'teams_id',
                            'compeititions_id'
                            ];

    public function team(){
        return $this->belongsTo('App\Team');
    }

}
