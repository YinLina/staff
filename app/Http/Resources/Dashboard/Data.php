<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Data extends ResourceCollection
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
            'data' => $this->collection->transform(function($data){
                return[
                    'day' => $data->day
                ];
            })
        ];
    }
}
