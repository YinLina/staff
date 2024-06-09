<?php

namespace App\Http\Resources\Position;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
class PositionData extends ResourceCollection
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
            'data' => $this->collection->transform(function($position){
                // <Select title from table> Modified by Huychoung 11/08/2023 #83
                $parentPosition = "";
                if($position->ParentPositions_id != null){
                    $dbPosition = DB::table('t_positions')
                                        ->select('title')
                                        ->where('id', $position->ParentPositions_id)
                                        ->first();
                    if($dbPosition != null) {
                        $parentPosition = $dbPosition->title;                
                    }
                }
                
                return[
                    'id' => $position->id,
                    'title' => $position->title,
                    'ParentPositions_id' => $position->ParentPositions_id,
                    'ParentPosition' => $parentPosition,
                    'Department_PKID' => $position->Department_PKID,
                    'department' => $position->department,
                    // 'employee' => $position->staffPosition,
                    'employee_count' => $position->staffPosition->count(),
                    'Remark' => $position->Remark
                ];
            })
        ];
    }
}
