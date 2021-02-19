<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AllFixture as AllFixtureResource;



class AllFixture extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
 
        return [
            'id' => $this->id,
            'currentMatchDay' => $this->competition->currentMatchDay,
            'matchDay' => $this->match_day,
            'status' => $this->status,
            'utcDate' => $this->utcDate,
            'lastUpdated' => $this->lastUpdated,
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'awayTeamCrestUrl' => $this->awayTeam->crestUrl,
            'homeTeamCrestUrl' => $this->homeTeam->crestUrl,
            'homeTeam' => $this->homeTeam->shortName,
            'awayTeam' => $this->awayTeam->shortName,
            'venue' => $this->homeTeam->venue,
            'competitionName' => $this->competition->name
        ];
    }
}
