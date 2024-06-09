<?php

namespace App\Http\Resources\DocumentType;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentData extends ResourceCollection
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
            'data' => $this->collection->transform(function($document){
                return[
                    'DocumentTypeID' => $document->DocumentTypeId,
                    'DocumentType' => $document->DocumentType,
                ];
            })
        ];
    }
}
