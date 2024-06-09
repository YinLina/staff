<?php

namespace App\Http\Resources\AddressCommune;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommuneData extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($commune){
                return[
                    'CommuneCode' => $commune->CommuneID,
                    'CommuneName' => $commune->CommuneName,
                ];
            })
        ];
    }
}