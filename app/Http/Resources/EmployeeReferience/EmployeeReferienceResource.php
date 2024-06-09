<?php

namespace App\Http\Resources\EmployeeReferience;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeReferienceResource extends JsonResource
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
            'data' => $this->collection->transform(function($referience){
                return[
                    'PKID' => $referience->PKID,
                    'Employee_PKID' => $referience->Employee_PKID,
                    'FullName' => $referience->FullName,
                    'Position' => $referience->Position,
                    'Company' => $referience->Company,
                    'Email' => $referience->Email,
                    'PhoneNumber' => $referience->PhoneNumber,
                ];
            })
        ];
    }
}
