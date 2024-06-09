<?php

namespace App\Http\Resources\StaffShift;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffShiftResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($staffShift){
                return[
                    'PKID' => $staffShift->PKID,
                    'Employees_id' => $staffShift->Employees_id,
                    'Employees_Name' => $staffShift->first_name . ' ' . $staffShift->last_name,
                    'ShiftHeader_PKID' => $staffShift->ShiftHeader_PKID,
                    'StartDate' => $staffShift->StartDate,
                    'EndDate' => $staffShift->EndDate,
                    'Remark' => $staffShift->Remark,
                    'ShiftName' => $staffShift->ShiftName,
                ];
            })
        ];
    }
}
