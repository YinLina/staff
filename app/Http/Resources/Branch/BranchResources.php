<?php

namespace App\Http\Resources\Branch;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BranchResources extends ResourceCollection
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
            'data' => $this->collection->transform(function($branch){
                return[
                    'PKID' => $branch->PKID,
                    'BranchCode' => $branch->BranchCode,
                    'BranchName' => $branch->BranchName,
                    'Location' => $branch->Location,
                    'CreatedDate' => $branch->CreatedDate,
                    'ClosedDate' => $branch->ClosedDate,
                    'Remark' => $branch->Remark,
                ];
            })
        ];
    }
}