<?php

namespace App\Http\Resources\SystemLog;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SystemLogResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            //modify LINA 01/09/2023 #cms-91
            'data' => $this->collection->transform(function($systemLog){
                return[
                    'PKID' => $systemLog->PKID,
                    'MenuName' => $systemLog->MenuName,
                    'User_PKID' => $systemLog->User_PKID,
                    'Action' => $systemLog->Action,
                    'OldData' => $systemLog->OldData,
                    'NewData' => $systemLog->NewData,
                    'LogType' => $systemLog->LogType,
                    'Message' => $systemLog->Message,
                    'HostName' => $systemLog->HostName,
                    'DateTime' => $systemLog->created_at,
                ];
            })
        ];
    }
}
