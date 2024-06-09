<?php

namespace App\Http\Resources\EmployeeOtherSkill;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeOtherSkillResource extends JsonResource
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
            'data' => $this->collection->transform(function($otherSkill){
                return[
                    'PKID' => $otherSkill->PKID,
                    'Employee_PKID' => $otherSkill->Employee_PKID,
                    'TrainingPlace' => $otherSkill->TrainingPlace,
                    'AddressOtherSkill' => $otherSkill->Address,
                    'StartDateOtherSkill' => $otherSkill->StartDate,
                    'EndDateOtherSkill' => $otherSkill->EndDate,
                    'Skill' => $otherSkill->Skill,
                    'Detail' => $otherSkill->Detail,
                ];
            })
        ];
    }
}
