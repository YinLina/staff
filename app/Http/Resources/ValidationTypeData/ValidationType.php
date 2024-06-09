<?php

namespace App\Http\Resources\ValidationTypeData;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ValidationType extends ResourceCollection
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
            'data' => $this->collection->transform(function($ValidationType){
                return[
                    'PKID' => $ValidationType->ValidationTypeId,
                    'ValidationType' => $ValidationType->ValidationType
                ];
            })
        ];
    }
}
