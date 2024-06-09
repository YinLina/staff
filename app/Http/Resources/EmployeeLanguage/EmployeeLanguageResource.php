<?php

namespace App\Http\Resources\EmployeeLanguage;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLanguageResource extends JsonResource
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
            'data' => $this->collection->transform(function($language){
                return[
                    'PKID' => $language->PKID,
                    'Employee_PKID' => $language->Employee_PKID,
                    'Language' => $language->Language,
                    'Level' => $language->Level,
                    'Detail' => $language->Detail,
                ];
            })
        ];
    }
}
