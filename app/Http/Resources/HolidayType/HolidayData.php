<?php

namespace App\Http\Resources\HolidayType;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HolidayData extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($holiday){
                return[
                    'HolidayTypeID' => $holiday->HolidayTypeId,
                    'HolidayType' => $holiday->HolidayType,
                ];
            })
        ];
    }
}
