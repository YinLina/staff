<?php

namespace App\Http\Resources\Department;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class DepartmentResources extends ResourceCollection
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
            
            // Modified by Huychoung 04/08/2023 #84
            'data' => $this->collection->transform(function($department){
                $parentDepartment = "";
                if($department->ParentDepartment_PKID != null) {
                    $dbDepartment = DB::table('t_department')
                    ->select('Department')
                    ->where('PKID', $department->ParentDepartment_PKID)
                    ->first();
                    if($dbDepartment != null) {
                        $parentDepartment = $dbDepartment->Department;
                    }
                    
                }
                return[
                    'PKID' => $department->PKID,
                    'Department' => $department->Department,
                    'ParentDepartment_PKID' => $department->ParentDepartment_PKID,
                    'ParentDepartment' => $parentDepartment,
                    'Remark' => $department->Remark,
                ]; 
            })
        ];
    }
}
