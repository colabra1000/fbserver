<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Team;

class Table extends Model
{
    

    public function team(){
        return $this->belongsTo('App\Team');
    }

}
