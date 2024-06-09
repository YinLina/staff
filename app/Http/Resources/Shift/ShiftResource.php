<?php

namespace App\Http\Resources\Shift;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShiftResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($shift){
                    // for($i = 0; $i < count($shift->shiftDetail); $i++) {
                    //     $timeInHour = explode(":", $shift->shiftDetail[$i]["TimeIn"])[0];
                    //     $timeInMinute = explode(":", $shift->shiftDetail[$i]["TimeIn"])[1];
                    //     if($timeInHour > 12) {
                    //         $calTimeInHour = $timeInHour - 12;
                    //         if($calTimeInHour == 10 || $calTimeInHour == 11) {
                    //             $shift->shiftDetail[$i]["TimeIn"] = $calTimeInHour .":". $timeInMinute . " PM";
                    //         } else {
                    //             $shift->shiftDetail[$i]["TimeIn"] = 0 . $calTimeInHour .":". $timeInMinute . " PM";
                    //         }
                    //     } else if($timeInHour == 0) {
                    //         $shift->shiftDetail[$i]["TimeIn"] = 12 .":". $timeInMinute . " AM";
                    //     } else if($timeInHour == 12) {
                    //         $shift->shiftDetail[$i]["TimeIn"] = 12 .":". $timeInMinute . " PM";
                    //     } else {
                    //         $shift->shiftDetail[$i]["TimeIn"] = $timeInHour .":". $timeInMinute . " AM";
                    //     }
    
                    //     $timeOutHour = explode(":", $shift->shiftDetail[$i]["TimeOut"])[0];
                    //     $timeOutMinute = explode(":", $shift->shiftDetail[$i]["TimeOut"])[1];
                    //     if($timeOutHour > 12) {
                    //         $calTimeOutHour = $timeOutHour - 12;
                    //         if($calTimeOutHour == 10 || $calTimeOutHour == 11) {
                    //             $shift->shiftDetail[$i]["TimeOut"] = $calTimeOutHour .":". $timeOutMinute . " PM";
                    //         } else {
                    //             $shift->shiftDetail[$i]["TimeOut"] = 0 . $calTimeOutHour .":". $timeOutMinute . " PM";
                    //         }
                    //     } else if($timeOutHour == 0) {
                    //         $shift->shiftDetail[$i]["TimeOut"] = 12 .":". $timeOutMinute . " AM";
                    //     } else if($timeOutHour == 12) {
                    //         $shift->shiftDetail[$i]["TimeOut"] = 12 .":". $timeOutMinute . " PM";
                    //     } else {
                    //         $shift->shiftDetail[$i]["TimeOut"] = $timeOutHour .":". $timeOutMinute . " AM";
                    //     }
                    // }
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
                    'Employee_Id' => $shift->Employees_id,
                    'ShiftDetail' => $shift->TimeIn. ' - '. $shift->TimeOut,
                    'ShiftDetailPKID' => $shift->PKID,
                ]; 
            }) 
        ];
    }
}