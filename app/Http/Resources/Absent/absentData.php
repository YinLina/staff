<?php

namespace App\Http\Resources\Absent;

use Illuminate\Http\Resources\Json\ResourceCollection;

class absentData extends ResourceCollection
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
            'data' => $this->collection->transform(function($absent){
                return[
                    'id' => $absent->id,
                    'day' => $absent->day,
                    'date' => $absent->date,
                    'desription' => $absent->description,
                    'employee' => [
                        'id' => $absent->employee->id,
                        'name' => $absent->employee->name,
                        'gender' => $absent->employee->gender,
                        'image' => $absent->employee->pic,
                        'position' => $absent->employee->position
                    ]
                ];
            })
        ];
    }
}
