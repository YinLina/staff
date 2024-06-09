<?php

namespace App\Http\Resources\StaffPosition;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffPositionResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($staffPosition){
                return[
                    'PKID' => $staffPosition->PKID,
                    'EmployeeId' => $staffPosition->first_name . ' ' .$staffPosition->last_name,
                    'Employees_id' => $staffPosition->Employees_id,
                    'Positions_id' => $staffPosition->Positions_id,
                    'PositionId' => $staffPosition->title,
                    'StartDate' => $staffPosition->StartDate,
                    'EndDate' => $staffPosition->EndDate,
                    'Remark' => $staffPosition->Remark,
                ];
            })
        ];
    }
}
