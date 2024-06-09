<?php

namespace App\Http\Resources\EmployeeExperience;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeExperienceResource extends JsonResource
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
            'data' => $this->collection->transform(function($experience){
                return[
                    'PKID' => $experience->PKID,
                    'Employee_PKID' => $experience->Employee_PKID,
                    'Company' => $experience->Company,
                    'AddressExperience' => $experience->Address,
                    'StartDateExperience' => $experience->StartDate,
                    'EndDateExperience' => $experience->EndDate,
                    'Position' => $experience->Position,
                    'Detail' => $experience->Detail,
                ];
            })
        ];
    }
}
