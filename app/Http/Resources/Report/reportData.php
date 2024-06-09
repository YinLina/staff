<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Resources\Json\ResourceCollection;

class reportData extends ResourceCollection
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
            'data' => $this->collection->transform(function($report){
                return[
                    'id' => $report->id,
                    'name' => $report->name,
                    'pic' => $report->pic,
                    'profile_color' => $report->profile_color,
                    'absent_count' => $report->absent_count,
                    'absent_total' => $report->absent_sum_number,
                    'absents' => $report->absent
                ];
            })
        ];
    }
}
