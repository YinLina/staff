<?php

namespace App\Http\Resources\Holiday;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HolidayResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($holiday){
                return[
                    'PKID' => $holiday->PKID,
                    'Description' => $holiday->Description,
                    'HolidayDate' => $holiday->HolidayDate,
                    'Allowed' => $holiday->Allowed == 1 ? true : false,
                    'HolidayType' => $holiday->HolidayType,
                    'Remark' => $holiday->Remark,
                ];
            })
        ];
    }
}

