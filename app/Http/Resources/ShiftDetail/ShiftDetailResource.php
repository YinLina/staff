<?php

namespace App\Http\Resources\ShiftDetail;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShiftDetailResource extends ResourceCollection
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
            //modify by LINA 21/09/2023 #cms-94
            'data' => $this->collection->transform(function($shift){
                    $timeInHour = explode(":", $shift->TimeIn);
                    $timeInMinute = explode(":", $shift->TimeIn);
                    $timeIn = (int)$timeInHour[0];
                    $timeInMn = (int)$timeInMinute[1];
                    if($timeIn > 12) {
                        $calTimeInHour = $timeIn - 12;
                        if($calTimeInHour == 10 || $calTimeInHour == 11) {
                            $shift->TimeIn = $calTimeInHour .":". $timeInMinute[1] . " PM";
                        } else {
                            $shift->TimeIn = 0 . $calTimeInHour .":". $timeInMinute[1] . " PM";
                        }
                    } else if($timeInHour[0] == 0) {
                        $shift->TimeIn = 12 .":". $timeInMinute[1] . " AM";
                    } else if($timeInHour[0] == 12) {
                        $shift->TimeIn = 12 .":". $timeInMinute[1] . " PM";
                    } else {
                        $shift->TimeIn = $timeInHour[0] .":". $timeInMinute[1] . " AM";
                    }
                    $timeOutHour = explode(":", $shift->TimeOut);
                    $timeOutMinute = explode(":", $shift->TimeOut);
                    if($timeOutHour > 12) {
                        $calTimeOutHour = (int)$timeOutHour[0] - 12;
                        if($calTimeOutHour == 10 || $calTimeOutHour == 11) {
                            $shift->TimeOut = $calTimeOutHour .":". $timeOutMinute[1] . " PM";
                        } else {
                            $shift->TimeOut = 0 . $calTimeOutHour .":". $timeOutMinute[1] . " PM";
                        }
                    } else if($timeOutHour == 0) {
                        $shift->TimeOut = 12 .":". $timeOutMinute[1] . " AM";
                    } else if($timeOutHour == 12) {
                        $shift->TimeOut = 12 .":". $timeOutMinute[1] . " PM";
                    } else {
                        $shift->TimeOut = $timeOutHour .":". $timeOutMinute[1] . " AM";
                    }
              
                return[
                    'PKID' => $shift->PKID,
                    'ShiftCode' => $shift->ShiftCode,
                    'ShiftName' => $shift->ShiftName,
                    'Remark' => $shift->Remark,
                    'Time' => $shift->TimeIn . ' - ' .$shift->TimeOut,
                ]; 
            }) 
        ];
    }
}