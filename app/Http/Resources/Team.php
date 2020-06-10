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
        
        return [
            'id' => $this->id,   
            'name' => $this->name,   
            'compeition_id' => $this->competition->id,   
            'compeition_name' => $this->competition->name,   
            'shortName' => $this->shortName,   
            'crestUrl' => $this->crestUrl,   
            'tla' => $this->tla,   
            'venue' => $this->venue,
            'founded' => $this->founded,
        ];
    }
}
