<?php

namespace App\Http\Resources\AddressVillage;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VillageData extends ResourceCollection
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
            'data' => $this->collection->transform(function($village){
                return[
                    'VillageCode' => $village->VillageID,
                    'VillageName' => $village->VillageName,
                ];
            })
        ];
    }
}