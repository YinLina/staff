<?php

namespace App\Http\Resources\LeaveEntry;

use App\Http\Controllers\Api\LeaveEntryController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Models\ShiftDetail;
class LeaveEntryResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($leaveEntry){
                //New
                $editTableArr = array();
                if($leaveEntry->PKID > 0) {
                    $applyLeaveDetail = DB::table('t_staff_apply_leave_detail')->where('StaffApplyLeaveHeaderPKID','=', $leaveEntry->PKID)->get();
                    if(count($applyLeaveDetail) != false) {
                        for($i = 0; $i < count($applyLeaveDetail); $i++) {
                            $editTableModel = new EditTableModel();
                            $editTableModel->attendanceDate = $applyLeaveDetail[$i]->AttendanceDate;
                            $shiftDetail = ShiftDetail::find($applyLeaveDetail[$i]->ShiftDetailPKID);
                            if($shiftDetail != null) {
                                $editTableModel->pkId = $shiftDetail->PKID;
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
                            array_push($editTableArr, $editTableModel);
                        }
                    }
                }

                return[
                    'PKID' => $leaveEntry->PKID,
                    'Staff' => $leaveEntry->first_name. ' '.$leaveEntry->last_name,
                    'StaffId' => $leaveEntry->StaffPKID,
                    'LeaveReason' => $leaveEntry->LeaveReason,
                    'LeaveType' => $leaveEntry->LeaveType,
                    'Remark' => $leaveEntry->Remark,
                    'Status'=> $leaveEntry->Status,
                    'editTableArr' => $editTableArr,
                    'Validationtype' => $leaveEntry->ValidationType,
                    'ValidationTypeID' => $leaveEntry->ValidationTypeId,
                    'LeaveTypePKID' => $leaveEntry->LeaveTypePKID,
                    'LeaveReasonPKID' => $leaveEntry->LeaveReasonPKID,
                    //mdif huychoung 06/07/2023 #75
                    'AttendanceDate' => $leaveEntry->AttendanceDate,
                    'StatusId' => $leaveEntry->StatusId,
                    

                    // modified by huychoung cms #100 12/10/2023
                    // 'DepartmentPKID' => $leaveEntry->DepartmentPKID,
                ];
            })
        ];
    }
}

class EditTableModel {
    public $pkId;
    public $attendanceDate;
    public $timeIn;
    public $timeOut;
}

