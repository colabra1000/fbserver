<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function competition(){
        return $this->belongsTo('App\Competition');
    }
}
