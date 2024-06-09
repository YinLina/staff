<?php

namespace App\Http\Resources\Document;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\Api\DocumentController;

class DocumentResource extends ResourceCollection
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
            // Modified by Huychoung 04/08/2023 #90
            'data' => $this->collection->transform(function($document){        
                $docController = new DocumentController();
                $documentType = $docController->readDocumentTypeForCombobox();
                $strType = "";
                for($i = 0; $i < count($documentType); $i++) {
                    if($document->Type == $documentType[$i]->id) {
                        $strType = $documentType[$i]->DocumentType;
                        break;
                    }
                }
                return[
                    'PKID' => $document->PKID,
                    'DocumentCode' => $document->DocumentCode,
                    'Title' => $document->Title,
                    'Type' => $document->Type,
                    'StrType' => $strType,
                    'ContentKH' => $document->ContentKH,
                    'ContentEN' => $document->ContentEN,
                    'DescriptionKH' => $document->DescriptionKH,
                    'DescriptionEN' => $document->DescriptionEN,
                    'FileName' => $document->FileName,
                ]; 
            })
        ];
    }
}
