<?php

namespace App\Http\Resources\StaffResignation;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
class StaffResignationResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($staffResignation){

                $resignationReason = "";
                if ($staffResignation->ResignationReason_PKID != null) {
                    $dbResignationReason = DB::table('t_resignation_reason')
                    ->select('Description')
                    ->where('PKID', $staffResignation->ResignationReason_PKID)
                    ->first();
                    if($dbResignationReason != null) {
                        $resignationReason = $dbResignationReason->Description;
                    }
                }
            
                return[
                    'PKID' => $staffResignation->PKID,
                    'Employee_PKID' => $staffResignation->Employee_PKID,
                    'EmployeeName' => $staffResignation->first_name . ' ' . $staffResignation->last_name,
                    'ResignationReason_PKID' => $staffResignation->ResignationReason_PKID,
                    'ResignationReason' => $resignationReason,
                    'Description' => $staffResignation->Description,
                    'ResignedDate' => $staffResignation->ResignedDate,   
                ];
            })
        ];
    }
}
