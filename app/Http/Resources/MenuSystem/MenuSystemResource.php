<?php

namespace App\Http\Resources\MenuSystem;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuSystemResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($menuSystem){
                return[
                    // 'PK_ID' => $menuSystem->PK_ID,
                    // 'PK_ID' => $menuSystem->id,
                    'id'=>$menuSystem->id,
                    'name'=>$menuSystem->name,
                    'url'=>$menuSystem->url,
                    'parent_id'=>$menuSystem->parent_id,
                    'icon'=>$menuSystem->icon,
                    'type'=>$menuSystem->type,
                    'order'=>$menuSystem->order,
                    'is_disable'=>$menuSystem->is_disable,

                ];
            })
        ];
    }
}
