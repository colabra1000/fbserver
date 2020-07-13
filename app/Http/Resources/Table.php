<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


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
            'teamCrestUrl' => $this->team->crestUrl,
            'teamShortName' => $this->team->shortName,
            'teamName' => $this->team->name,
            'goalAgainst' => $this->goalsAgainst,
            'goalDifference' => $this->goalDifference,
            'competition_id' => $this->competition_id
        ];
    }
}
