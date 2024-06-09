<?php

namespace App\Http\Resources\LeaveType;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeaveTypeData extends ResourceCollection
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
            'data' => $this->collection->transform(function($leaveType){
                return[
                    'PK_ID' => $leaveType->PK_ID,
                    'LeaveType' => $leaveType->LeaveType,
                    'Allowance' => $leaveType->Allowance,
                    'Deduction' => $leaveType->Deduction,
                    'Remark' => $leaveType->Remark,
                ];
            })
        ];
    }
}
