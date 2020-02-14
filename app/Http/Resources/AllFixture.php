<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'match_day' => $this->match_day,
            'status' => $this->status,
            'utcDate' => $this->utcDate,
            'lastUpdated' => $this->lastUpdated,
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'homeTeam' => $this->homeTeam->shortName,
            'awayTeam' => $this->awayTeam->shortName,
            // 'match' => $this->match,
            'competition' => $this->competition,
            'venue' => $this->homeTeam->venue,
        ];
    }
}
