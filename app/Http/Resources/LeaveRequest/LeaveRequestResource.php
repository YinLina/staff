<?php

namespace App\Http\Resources\LeaveRequest;
use App\Models\ShiftDetail;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\ApplyLeaveDetail;
use Illuminate\Support\Facades\DB;
class LeaveRequestResource extends ResourceCollection
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
            'data' => $this->collection->transform(function($leaveRequest){
                $editTableArr = array();
                    $applyleave_detail = DB::table('t_staff_apply_leave_detail')->where('StaffApplyLeaveHeaderPKID',$leaveRequest->PKID)->get();
                    if(count($applyleave_detail) != false){
                        foreach($applyleave_detail as $data) {
                            $editTableModel = new EditTableModel();
                            $editTableModel->attendanceDate = $data->AttendanceDate;
                            $shiftDetail = ShiftDetail::find($data->ShiftDetailPKID);
                            if($shiftDetail != null) {
                                $editTableModel->pkId = $shiftDetail["PKID"];
                                $editTableModel->timeIn = $shiftDetail["TimeIn"];
                                $editTableModel->timeOut = $shiftDetail["TimeOut"];
                                $editTableModel->TimeInTimeOut = $shiftDetail["TimeIn"] ." - ". $shiftDetail["TimeOut"];
                                    $timeInHour = explode(":", $editTableModel->timeIn)[0];
                                    $timeInMinute = explode(":", $editTableModel->timeOut)[1];
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

                                    $timeOutHour = explode(":", $editTableModel->timeOut)[0];
                                    $timeOutMinute = explode(":", $editTableModel->timeOut)[1];
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
                
                return[                    
                    'PKID' => $leaveRequest->PKID,
                    'StaffName' => $leaveRequest->first_name. ' '.$leaveRequest->last_name,
                    'SubmittedDate' => $leaveRequest->SubmittedDate,
                    'LeaveReason' => $leaveRequest->LeaveReason,
                    'LeaveType' => $leaveRequest->LeaveType,
                    'Status' => $leaveRequest->Status,
                    'Remark' => $leaveRequest->Remark,
                    'editTableArr' => $editTableArr,
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
    public $TimeInTimeOut;
}
