<?php

namespace App\Http\Resources\AddressDistrict;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DistrictData extends ResourceCollection
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
            'data' => $this->collection->transform(function($district){
                return[
                    'DistrictCode' => $district->DistrictID,
                    'ProvinceCode' => $district->ProvinceCode,
                    'DistrictName' => $district->DistrictName,
                ];
            })
        ];
    }
}