<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Scorer extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

           
            'id' => $this->id,
            'numberOfGoals' => $this->numberOfGoals,
            'player_id' => $this->player_id,
            'competition_id' => $this->competition_id,
            'player_name' => $this->player_name,
            'player_nationality' => $this->player_nationality,
            'player_position' => $this->player_position,
            'player_shirtNumber' => $this->player_shirtNumber,
            'team_name' => $this->team->name,
        
        ];
    }
}
