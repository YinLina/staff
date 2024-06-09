<?php

namespace App\Http\Resources\ResignationReason;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResignationReasonResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($resignationReason){

                return[
                    
                    'PKID' => $resignationReason->PKID, 
                    'ReasonCode' => $resignationReason->ReasonCode,
                    'Description' => $resignationReason->Description,
                ];
            })
        ];
    }
}
