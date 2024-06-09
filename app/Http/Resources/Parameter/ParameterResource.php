<?php

namespace App\Http\Resources\Parameter;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ParameterResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($parameter){
                return[
                    'PKID' => $parameter->PKID,
                    'ParameterCode' => $parameter->ParameterCode,
                    'ValueOne' => $parameter->ValueOne,
                    'ValueTwo' => $parameter->ValueTwo,
                    'Remark' => $parameter->Remark,
                    'TextKH' => $parameter->TextKH,
                    'TextEN' => $parameter->TextEN,
                    'TextVN' => $parameter->TextVN,
                    'TextTH' => $parameter->TextTH,
                    'TextCH' => $parameter->TextCH,
                    'TextFR' => $parameter->TextFR,
                ];
            })
        ];
    }
}
