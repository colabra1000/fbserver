<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\AllFixture as AllFixtureResource;
use App\AllFixture;

class Team extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $nextMatchDay =  $this->standing->playedGames + 1;
        $previousMatchDay = $this->standing->playedGames;
        return [
            'id' => $this->id,   
            'name' => $this->name,   
            'compeition_id' => $this->competition->id,   
            'compeition_name' => $this->competition->name,  
            'country' => $this->competition->country, 
            'shortName' => $this->shortName,   
            'crestUrl' => $this->crestUrl,   
            'tla' => $this->tla,   
            'table' => $this->standing,
            'venue' => $this->venue,
            'founded' => $this->founded,
            // 'nextMatch' => $this->nextMatch(),
            // 'previousMatch' => $this->previousMatch(),
            'nextMatch' => new AllFixtureResource(AllFixture::where('match_day', $nextMatchDay)->where('competition_id', $this->competition->id)->where(function($query){$query->where('homeTeam_id', $this->id)->orWhere('awayTeam_id', $this->id);})->first()),
            'previousMatch' => new AllFixtureResource(AllFixture::where('match_day', $previousMatchDay)->where('competition_id', $this->competition->id)->where(function($query){$query->where('homeTeam_id', $this->id)->orWhere('awayTeam_id', $this->id);})->first()), 
            
        ];
    }
}
