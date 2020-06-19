<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class TeamGetResource extends JsonResource
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
            'teamName' => $this->name,
            'shortName' => $this->shortName,
            'crestUrl' => $this->crestUrl,
            'venue' => $this->venue,
            'founded' => $this->founded,
            'competitionId' => $this->competition->id,
            'nextFixture' => '',
            'previousFixture' => '',
        ];

    }
}
