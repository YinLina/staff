<?php

namespace App\Http\Resources\LeaveReason;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeaveReasonData extends ResourceCollection
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
            'data' => $this->collection->transform(function($leaveReason){
                return[
                    'PK_ID' => $leaveReason->PK_ID,
                    'LeaveReason' => $leaveReason->LeaveReason,
                    'Remark' => $leaveReason->Remark,
                ];
            })
        ];
    }
}
