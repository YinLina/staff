<?php

namespace App\Http\Resources\Education;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeEducationResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($education){
                return[
                    'PKID' => $education->PKID,
                    'Employee_PKID' => $education->Employee_PKID,
                    'SchoolName' => $education->SchoolName,
                    'Address' => $education->Address,
                    'StartDate' => $education->StartDate,
                    'EndDate' => $education->EndDate,
                    'Skill' => $education->Skill,
                    'Detail' => $education->Detail,
                ];
            })
        ];
    }
}
