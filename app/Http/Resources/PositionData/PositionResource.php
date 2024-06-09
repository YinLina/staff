<?php

namespace App\Http\Resources\PositionData;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class PositionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'data' => $this->collection->transform(function($position){
                return[
                    'PositionPKID' => $position->id,
                    'Position' => $position->title,
                ];
            })
        ];
    }
}
