<?php

namespace App\Http\Resources\ApplyLeave;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Models\ShiftDetail;
use App\Models\User;
use App\Models\WorkflowGroup;

class ApplyLeaveData extends ResourceCollection
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
            'data' => $this->collection->transform(function($ApplyLeave){
                $editTableArr = array();
                if($ApplyLeave->PKID > 0) {
                    $applyLeaveDetail = DB::table('t_staff_apply_leave_detail')->where('StaffApplyLeaveHeaderPKID','=', $ApplyLeave->PKID)->get();
                    if(count($applyLeaveDetail) != false) {
                        for($i = 0; $i < count($applyLeaveDetail); $i++) {
                            $editTableModel = new EditTableModel();
                            $editTableModel->AttendanceDate = $applyLeaveDetail[$i]->AttendanceDate;
                            $shiftDetail = ShiftDetail::find($applyLeaveDetail[$i]->ShiftDetailPKID);
                            if($shiftDetail != null) {
                                $editTableModel->PKID = $shiftDetail->PKID;
                                $editTableModel->TimeInTimeOutId = $shiftDetail->PKID;
                                $timeInHour = explode(":", $shiftDetail->TimeIn)[0];
                                $timeInMinute = explode(":", $shiftDetail->TimeOut)[1];
                                if($timeInHour > 12) {
                                    $calTimeInHour = $timeInHour - 12;
                                    if($calTimeInHour == 10 || $calTimeInHour == 11) {
                                        $editTableModel->timeIn = $calTimeInHour .":". $timeInMinute . " PM";
                                    } else {
                                        $editTableModel->timeIn = 0 . $calTimeInHour .":". $timeInMinute . " PM";
                                    }
                                } else if($timeInHour == 0) {
                                    $editTableModel->timeIn = 12 .":". $timeInMinute . " AM";
                                } else if($timeInHour == 12) {
                                    $editTableModel->timeIn = 12 .":". $timeInMinute . " PM";
                                } else {
                                    $editTableModel->timeIn = $timeInHour .":". $timeInMinute . " AM";
                                }

                                $timeOutHour = explode(":", $shiftDetail->TimeOut)[0];
                                $timeOutMinute = explode(":", $shiftDetail->TimeOut)[1];
                                if($timeOutHour > 12) {
                                    $calTimeOutHour = $timeOutHour - 12;
                                    if($calTimeOutHour == 10 || $calTimeOutHour == 11) {
                                        $editTableModel->timeOut = $calTimeOutHour .":". $timeOutMinute . " PM";
                                    } else {
                                        $editTableModel->timeOut = 0 . $calTimeOutHour .":". $timeOutMinute . " PM";
                                    }
                                } else if($timeOutHour == 0) {
                                    $editTableModel->timeOut = 12 .":". $timeOutMinute . " AM";
                                } else if($timeOutHour == 12) {
                                    $editTableModel->timeOut = 12 .":". $timeOutMinute . " PM";
                                } else {
                                    $editTableModel->timeOut = $timeOutHour .":". $timeOutMinute . " AM";
                                }
                            }
                            if($editTableModel->timeIn != null && $editTableModel->timeOut != null) {
                                $editTableModel->TimeInTimeOutCode = $editTableModel->timeIn ."-". $editTableModel->timeOut;
                            } else {
                                $editTableModel->TimeInTimeOutCode = "";
                            }
                            array_push($editTableArr, $editTableModel);
                        }
                    }
                }
                return[
                    'PKID' => $ApplyLeave->PKID,
                    'Staff' => $ApplyLeave->first_name . ' ' .$ApplyLeave->last_name,
                    'LeaveReason' => $ApplyLeave->LeaveReason,
                    'LeaveType' => $ApplyLeave->LeaveType,
                    'Status' => $ApplyLeave->Status,
                    'Remark' => $ApplyLeave->Remark,
                    'editTableArr' => $editTableArr,
                    'LeaveTypePKID' => $ApplyLeave->LeaveTypePKID,
                    'LeaveReasonPKID' => $ApplyLeave->LeaveReasonPKID,
                    'StaffPKID' => $ApplyLeave->StaffPKID,
                    'StatusID' => $ApplyLeave->StatusId,
                    'ValidationType' => $ApplyLeave->ValidationType,
                    'ValidationTypeID' => $ApplyLeave->ValidationTypeId,
                ];
            })
        ];
    }
}

class EditTableModel {
    public $PKID;
    public $TimeInTimeOutId;
    public $AttendanceDate;
    public $timeIn;
    public $timeOut;
    public $TimeInTimeOutCode;
}

