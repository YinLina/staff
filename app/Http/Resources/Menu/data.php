<?php

namespace App\Http\Resources\Menu;

use App\Models\Menu;
use Illuminate\Http\Resources\Json\ResourceCollection;

class data extends ResourceCollection
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
            'data' => $this->collection->transform(function($menu){
                return[
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'parent' =>  ($menu->parent),
                ];
            })
        ];
    }
}
