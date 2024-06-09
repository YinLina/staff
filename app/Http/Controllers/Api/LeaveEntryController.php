<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplyLeave;
use App\Models\ApplyLeaveDetail;
use App\Http\Resources\LeaveEntry\LeaveEntryResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Auth;

class LeaveEntryController extends Controller
{
    public function read(Request $Request)
    {
        //dd(Carbon::parse('2017-02-06 22:25:12'));
        $dateVal = $Request->quickFilter;
        $typeVal = $Request->typeFilter;
        $startDate = $Request->startDate;
        $endDate = $Request->endDate;
        $this->CreateValidationStatusTemporary();
        $this->CreateValidationTypeTemporary();
        $leaveEntry = DB::table('t_staff_apply_leave_header')
            ->join('t_employees', 't_employees.id', '=', 't_staff_apply_leave_header.StaffPKID')
            ->join('t_users', 't_users.employee_id', '=', 't_employees.id')
            ->join('t_leave_reason', 't_leave_reason.PK_ID', '=', 't_staff_apply_leave_header.LeaveReasonPKID')
            ->join('t_leave_type', 't_leave_type.PK_ID', '=', 't_staff_apply_leave_header.LeaveTypePKID')
            ->join('temp_ValidationStatus', 'temp_ValidationStatus.StatusId', '=', 't_staff_apply_leave_header.Status')
            ->join('temp_ValidationType', 'temp_ValidationType.ValidationTypeId', 't_staff_apply_leave_header.ValidationType')
            ->join('t_staff_apply_leave_detail', 't_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID', 't_staff_apply_leave_header.PKID')
            ->select('first_name', 'last_name', 'LeaveReason', 'LeaveType', 't_staff_apply_leave_header.Remark', 'temp_ValidationStatus.Status', 'StatusId', 't_staff_apply_leave_header.PKID', 'LeaveReasonPKID', 'LeaveTypePKID', 'StaffPKID', 'temp_ValidationType.ValidationType', 'temp_ValidationType.ValidationTypeId', 'AttendanceDate')->distinct();

        if ($dateVal != 0 || count($typeVal) > 0 || ($startDate != "" && $endDate != "")) {
            if ($startDate != "" && $endDate != "") {
                $carBonStartDate = Carbon::parse($startDate);
                $carBonEndDate = Carbon::parse($endDate);
                $leaveEntry = $leaveEntry->where('AttendanceDate', '>=', $carBonStartDate)
                    ->where('AttendanceDate', '<=', $carBonEndDate);
            }
            if (count($typeVal) > 0) {
                $leaveEntry = $leaveEntry->whereIn('ValidationTypeId', $typeVal);
            }
            if ($dateVal != 0) {
                $filterDate = Carbon::today();
                if ($dateVal == FilterDate::Today->value) {
                    $filterDate = Carbon::today();
                    $leaveEntry = $leaveEntry->where('AttendanceDate', $filterDate);
                } else if ($dateVal == FilterDate::Weekly->value) {
                    $firstDayOfWeek = Carbon::now()->startOfWeek();
                    $endDayOfWeek = Carbon::now()->endOfWeek();
                    $leaveEntry = $leaveEntry->where('AttendanceDate', '>=', $firstDayOfWeek)
                        ->where('AttendanceDate', '<=', $endDayOfWeek);
                } else if ($dateVal == FilterDate::Monthly->value) {
                    $firstDayOfMonth = Carbon::now()->startOfMonth();
                    $lastDayOfMonth = Carbon::now()->endOfMonth();
                    $leaveEntry = $leaveEntry->where('AttendanceDate', '>=', $firstDayOfMonth)
                        ->where('AttendanceDate', '<=', $lastDayOfMonth);
                } else {
                    $firstDayOfYear = Carbon::now()->startOfYear();
                    $lastDayOfYear = Carbon::now()->endOfYear();
                    $leaveEntry = $leaveEntry->where('AttendanceDate', '>=', $firstDayOfYear)
                        ->where('AttendanceDate', '<=', $lastDayOfYear);
                }
            }
            $leaveEntry = $leaveEntry->get();
        } else {
            $leaveEntry = $leaveEntry->where('StatusId', 3)->orWhere('StatusId', 5)->get();
        }
        Schema::drop('temp_ValidationType');
        Schema::drop('temp_ValidationStatus');
        return new LeaveEntryResource($leaveEntry);
    }

    public function readStaff($id) {
        // $staff = DB::table('t_staff_position')
        // ->join('t_employees', 't_employees.id', '=', 't_staff_position.Employees_id')
        // ->select('t_employees.first_name', 't_employees.last_name')->get();
        $staff = DB::table('t_department')
        ->join('t_positions', 't_positions.Department_PKID', '=', 't_department.PKID')
        ->join('t_staff_position', 't_staff_position.Positions_id', '=', 't_positions.id')
        ->join('t_employees', 't_staff_position.Employees_id', '=', 't_employees.id')
        ->where('t_department.PKID', '=', $id)
        ->select('t_employees.id', 't_employees.first_name', 't_employees.last_name', 't_employees.ShiftHeaderPKID')->get()->toArray();
        $listStaff = array();
        for($i = 0; $i < count($staff); $i++) {
            $timeInTimeOuts = array();
            $staffModel = new StaffModel();
            $shiftHeaderPKID = $staff[$i]->ShiftHeaderPKID;
            $shiftDetail = DB::table('t_shift_detail')
            ->where('t_shift_detail.ShiftHeaderPKID', '=', $shiftHeaderPKID)
            ->select('t_shift_detail.TimeIn', 't_shift_detail.TimeOut', 't_shift_detail.PKID')->get();
            for($j = 0; $j < count($shiftDetail); $j++) {
                $timeInTimeOutModel = new TimeInTimeModel();
                $timeInHour = explode(":", $shiftDetail[$j]->TimeIn)[0];
                $timeInMinute = explode(":", $shiftDetail[$j]->TimeOut)[1];
                
                // Time In
                if($timeInHour > 12) {
                    $calTimeInHour = $timeInHour - 12;
                    if($calTimeInHour == 10 || $calTimeInHour == 11) {
                        $timeInTimeOutModel->timeIn = $calTimeInHour .":". $timeInMinute . " PM";
                    } else {
                        $timeInTimeOutModel->timeIn = 0 . $calTimeInHour .":". $timeInMinute . " PM";
                    }
                } else if($timeInHour == 0) {
                    $timeInTimeOutModel->timeIn = 12 .":". $timeInMinute . " AM";
                } else if($timeInHour == 12) {
                    $timeInTimeOutModel->timeIn = 12 .":". $timeInMinute . " PM";
                } else {
                    $timeInTimeOutModel->timeIn = $timeInHour .":". $timeInMinute . " AM";
                }
                
                // Time Out
                $timeOutHour = explode(":", $shiftDetail[$j]->TimeOut)[0];
                $timeOutMinute = explode(":", $shiftDetail[$j]->TimeOut)[1];
                if($timeOutHour > 12) {
                    $calTimeOutHour = $timeOutHour - 12;
                    if($calTimeOutHour == 10 || $calTimeOutHour == 11) {
                        $timeInTimeOutModel->timeOut = $calTimeOutHour .":". $timeOutMinute . " PM";
                    } else {
                        $timeInTimeOutModel->timeOut = 0 . $calTimeOutHour .":". $timeOutMinute . " PM";
                    }
                } else if($timeOutHour == 0) {
                    $timeInTimeOutModel->timeOut = 12 .":". $timeOutMinute . " AM";
                } else if($timeOutHour == 12) {
                    $timeInTimeOutModel->timeOut = 12 .":". $timeOutMinute . " PM";
                } else {
                    $timeInTimeOutModel->timeOut = $timeOutHour .":". $timeOutMinute . " AM";
                }
                $timeInTimeOutModel->PKID = $shiftDetail[$j]->PKID;
                $timeInTimeOutModel->TimeInPlusTimeOut = $timeInTimeOutModel->timeIn . " - " . $timeInTimeOutModel->timeOut;
                array_push($timeInTimeOuts, $timeInTimeOutModel);
            }
            $staffModel->staffId = $staff[$i]->id;
            $staffModel->first_name = $staff[$i]->first_name;
            $staffModel->last_name = $staff[$i]->last_name;
            $staffModel->ShiftHeaderPKID = $staff[$i]->ShiftHeaderPKID;
            $staffModel->TimeInTimeOuts = $timeInTimeOuts;
            array_push($listStaff, $staffModel);
        }
        return response()->json(['Data' => $listStaff]);
    }

    public function createStaffApplyLeave(Request $request) {
        $DataForm = $request->DataForm;
        for($i = 0; $i < count($DataForm); $i++) {
            $staffId = $DataForm[$i]['StaffId'];
            $leaveTypeId = $DataForm[$i]['LeaveTypePKID'];
            $leaveReasonId = $DataForm[$i]['LeaveReasonPKID'];
            $attendanceDate = $DataForm[$i]['AttendanceDate'];
            $shiftDetailId = $DataForm[$i]['TimeInTimeOutId'];
            $permission = $DataForm[$i]['Permission'];
            $absent = $DataForm[$i]['Absent'];

            // save into table t_staff_apply_leave_header
            $staffApplyLeaveHeader = new ApplyLeave();  
            $staffApplyLeaveHeader->Remark = "";
            $staffApplyLeaveHeader->StaffPKID = $staffId;
            $staffApplyLeaveHeader->LeaveReasonPKID = $leaveReasonId;
            $staffApplyLeaveHeader->LeaveTypePKID = $leaveTypeId;
            date_default_timezone_set('Asia/Phnom_Penh');
            $staffApplyLeaveHeader->CreatedDate = date('Y-m-d H:i:s');
            $staffApplyLeaveHeader->Status = 5;
            $staffApplyLeaveHeader->ValidationType = 1;
            $staffApplyLeaveHeader->save();

            // save into table t_staff_apply_leave_details
            $staffApplyLeaveDetail = new ApplyLeaveDetail();
            $staffApplyLeaveDetail->StaffApplyLeaveHeaderPKID = $staffApplyLeaveHeader->PKID;
            $staffApplyLeaveDetail->AttendanceDate = $attendanceDate;
            $staffApplyLeaveDetail->ShiftDetailPKID = $shiftDetailId;
            $staffApplyLeaveDetail->Permission = $permission;
            $staffApplyLeaveDetail->Absent = $absent;
            $staffApplyLeaveDetail->save();
        }
        return response()->json(['message' => 'Your data was successfully saved.', 200]);
    }
    public function create(Request $Request){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($Request, [
                'LeaveReasonPKID' => 'required',
                'LeaveTypePKID' => 'required',
                'StaffPKID' => 'required',
                'ValidationType' => 'required',
                'DataForm' => 'required',
            ], [
                'LeaveReasonPKID.required' => 'The name field is required.',
                'LeaveTypePKID.required' => 'The name field is required.',
                'StaffPKID.required' => 'The name field is required.',
            ]);
            $leaveEntry = new ApplyLeave();
            $leaveEntry->Remark = $Request->Remark;
            $leaveEntry->StaffPKID = $Request->StaffPKID;
            $leaveEntry->LeaveReasonPKID = $Request->LeaveReasonPKID;
            $leaveEntry->LeaveTypePKID = $Request->LeaveTypePKID;
            date_default_timezone_set('Asia/Phnom_Penh');
            $leaveEntry->CreatedDate = date('Y-m-d H:i:s');
            $leaveEntry->Status = 5;
            $leaveEntry->ValidationType = $Request->ValidationType;
            $leaveEntry->save();
            $DateAndTime = $Request->DataForm;
            //modify by Theara #CMS-78 11/07/23
            foreach ($DateAndTime as $DateTime) {
                $filter = Arr::where($DateAndTime, function ($value, $key) use ($DateTime) {
                    return $value['AttendanceDate'] == $DateTime['AttendanceDate'] && $value['TimeInTimeOutId'] == $DateTime['TimeInTimeOutId'];
                });
                if (count($filter) >= 2) {
                    $this->validate($Request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required',
                    ], [
                        'AttendanceDate.required' => 'Data is duplicated.'
                    ]);
                } else if ($DateTime['AttendanceDate'] == null || $DateTime['TimeInTimeOutId'] == null) {
                    $this->validate($Request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required'
                    ], [
                        'AttendanceDate.required' => 'Data can not be empity.'
                    ]);
                }
                $DuplicateData = DB::table('t_staff_apply_leave_header')
                    ->join('t_staff_apply_leave_detail', 't_staff_apply_leave_header.PKID', 't_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID')
                    ->where('StaffPKID', $Request->StaffPKID)
                    ->where('AttendanceDate', $DateTime['AttendanceDate'])
                    ->where('ShiftDetailPKID', $DateTime['TimeInTimeOutId'])
                    ->get();
                if (count($DuplicateData) > 0) {
                    $this->validate($Request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required',
                    ], [
                        'AttendanceDate.required' => 'Data is already existed.'
                    ]);
                }
                $ApplyLeaveDetail = new ApplyLeaveDetail();
                $ApplyLeaveDetail->StaffApplyLeaveHeaderPKID = $leaveEntry->PKID;
                $ApplyLeaveDetail->AttendanceDate = $DateTime['AttendanceDate'];
                $ApplyLeaveDetail->ShiftDetailPKID = $DateTime['TimeInTimeOutId'];
                $ApplyLeaveDetail->save();
            }

            $this->InsertSysLog('LeaveEntry', $userPKID, 'Create', '', 'PK_ID: ' . $leaveEntry->PKID . ' StaffPKID: ' .$Request->StaffPKID, 'Success', '');
            return response()->json(['message' => 'Your data was successfully saved.', 200]);
        } catch (Exception $showMessage) {
            $this->InsertSysLog('LeaveEntry', $userPKID, 'Create', '', 'Error: ' . $leaveEntry->StaffPKID, 'Error', $showMessage);
            return response()->json([
                'errors' => $showMessage->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'LeaveReasonPKID' => 'required',
                'LeaveTypePKID' => 'required',
                'StaffPKID' => 'required',
                'ValidationType' => 'required',
                'DataForm' => 'required',
            ], [
                'LeaveReasonPKID.required' => 'The name field is required.',
                'LeaveTypePKID.required' => 'The name field is required.',
                'StaffPKID.required' => 'The name field is required.',
            ]);
            $DateAndTime = $request->DataForm;
            foreach ($DateAndTime as $DateTime) {
                $filter = Arr::where($DateAndTime, function ($value, $key) use ($DateTime) {
                    return $value['AttendanceDate'] == $DateTime['AttendanceDate'] && $value['TimeInTimeOutId'] == $DateTime['TimeInTimeOutId'];
                });
                if (count($filter) >= 2) {
                    $this->validate($request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required',
                    ], [
                        'AttendanceDate.required' => 'Data is duplicated.'
                    ]);
                } else if ($DateTime['AttendanceDate'] == null || $DateTime['TimeInTimeOutId'] == null) {
                    $this->validate($request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required'
                    ], [
                        'AttendanceDate.required' => 'Data can not be empity.'
                    ]);
                }
                // $DuplicateData = DB::table('t_staff_apply_leave_header')
                // ->join('t_staff_apply_leave_detail','t_staff_apply_leave_header.PKID','t_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID')
                // ->where('StaffPKID', $request->StaffPKID)
                // ->where('AttendanceDate', $DateTime['AttendanceDate'])
                // ->where('ShiftDetailPKID', $DateTime['TimeInTimeOutId'])
                // ->get();
                // if(count($DuplicateData) > 0){
                //     $this->validate($request,[
                //         'AttendanceDate' => 'required',
                //         'TimeInTimeOutId' => 'required',
                //     ],[
                //         'AttendanceDate.required' => 'Data is already existed.'
                //     ]);
                // }
            }
            $leaveEntry = ApplyLeave::findOrFail($id);
            $oldLeaveEntryData = 'PK_ID: ' . $leaveEntry->PKID . ' Staff: ' . $leaveEntry->Staff;
            $leaveEntry->Remark = $request->Remark;
            $leaveEntry->StaffPKID = $request->StaffPKID;
            $leaveEntry->LeaveReasonPKID = $request->LeaveReasonPKID;
            $leaveEntry->LeaveTypePKID = $request->LeaveTypePKID;
            date_default_timezone_set("Asia/Phnom_Penh");
            $leaveEntry->SubmittedDate = date('Y-m-d H:i:s');
            $leaveEntry->Status = 5;
            $leaveEntry->ValidationType = $request->ValidationType;
            $leaveEntry->save();
            DB::delete('DELETE FROM t_staff_apply_leave_detail WHERE StaffApplyLeaveHeaderPKID = ' . $id);
            //modify by Theara #CMS-78 11/07/23
            foreach ($DateAndTime as $DateTime) {
                $ApplyLeaveDetail = new ApplyLeaveDetail();
                $ApplyLeaveDetail->StaffApplyLeaveHeaderPKID = $leaveEntry->PKID;
                $ApplyLeaveDetail->AttendanceDate = $DateTime['AttendanceDate'];
                $ApplyLeaveDetail->ShiftDetailPKID = $DateTime['TimeInTimeOutId'];
                $ApplyLeaveDetail->save();
            }

            $this->InsertSysLog('LeaveEntry', $userPKID, 'Update', $oldLeaveEntryData, 'PK_ID: ' . $leaveEntry->PKID . ' Staff: ' . $request->Staff, 'Success', '');
            return response()->json(['message' => 'Your data was updated successfully.'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('LeaveEntry', $userPKID, 'Update', $oldLeaveEntryData, 'PK_ID: ' . $leaveEntry->PKID . ' Staff Error: ' . $request->Staff . ' Error choose fields', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete($id){
        try{
            $userPKID = Auth::user()->id;
            $leaveType = ApplyLeave::findOrFail($id);
            DB::delete('DELETE FROM t_staff_apply_leave_detail WHERE StaffApplyLeaveHeaderPKID = ' . $id);
            $leaveType->delete();

            $this->InsertSysLog('LeaveEntry', $userPKID, 'Delete', 'PK_ID: ' . $leaveType->PKID . ' Staff: ' . $leaveType->StaffPKID, '', 'Error', '');
            return response()->json(['message' => 'Your data was deleted successfully.'], 200);
            
        } catch (Exception $e) {
            $this->InsertSysLog('LeaveEntry', $userPKID, 'Delete', 'PK_ID: ' . $leaveType->PKID . ' Staff Error: ' . $leaveType->StaffPKID, '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
}

enum FilterDate: int
{
    case Today = 1;
    case Weekly = 2;
    case Monthly = 3;
    case Yearly = 4;
}

class TimeInTimeModel {
    public $PKID;
    public $timeIn;
    public $timeOut;
    public $TimeInPlusTimeOut;
}

class StaffModel {
    public $staffId;
    public $first_name;
    public $last_name;
    public $ShiftHeaderPKID;
    public $TimeInTimeOuts = array();
}