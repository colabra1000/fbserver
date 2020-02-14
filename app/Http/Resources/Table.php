<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Team as TeamResource;
use App\Team;

class Table extends JsonResource
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
            'position' => $this->position,
            'playedGames' => $this->playedGames,
            'won' => $this->won,
            'draw' => $this->draw,
            'lost' => $this->lost,
            'points' => $this->points,
            'goalFor' => $this->goalsFor,
            'team' => $this->team,
            'competition' => $this->team->competition,
            'goalAgainst' => $this->goalsAgainst,
            'goalDifference' => $this->goalDifference,
        ];
    }
}
