<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        // return parent::toArray($request);
        return [
            'id' => $this->id,   
            'name' => $this->name,   
            'compeition' => $this->competition,   
            'shortName' => $this->shortName,   
            'crestUrl' => $this->crestUrl,   
            'tla' => $this->tla,   
            'venue' => $this->venue,
        ];
    }
}
