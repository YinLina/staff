<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequestModel;
use App\Models\ApplyLeave;
use App\Models\ValidationProcess;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\LeaveRequest\LeaveRequestResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function read() {
        $this-> CreateValidationStatusTemporary();
        $userId = Auth::user()->id;
        $leaveRequest = DB::table('t_staff_apply_leave_header')
        ->join('t_employees','t_employees.id','t_staff_apply_leave_header.StaffPKID')
        ->join('t_leave_reason','t_leave_reason.PK_ID','t_staff_apply_leave_header.LeaveReasonPKID')
        ->join('t_leave_type', 't_leave_type.PK_ID', 't_staff_apply_leave_header.LeaveTypePKID')
        ->join('temp_ValidationStatus','temp_ValidationStatus.StatusId','t_staff_apply_leave_header.Status')
        ->join('t_validation_process', 't_validation_process.StaffApplyLeaveHeaderPKID', 't_staff_apply_leave_header.PKID')
        ->join('t_group_user', 't_group_user.GroupPKID', 't_validation_process.GroupPKID')
        ->select(
            't_staff_apply_leave_header.PKID',
            't_employees.first_name','t_employees.last_name',
            't_staff_apply_leave_header.SubmittedDate',
            't_leave_reason.LeaveReason',
            'temp_ValidationStatus.Status',
            't_leave_type.LeaveType',
            't_staff_apply_leave_header.Remark'
        )
        ->where('t_group_user.UserPKID', $userId)
        ->where('t_validation_process.Status',1)
        ->get();
        Schema::drop('temp_ValidationStatus');
        return new LeaveRequestResource($leaveRequest);
    }

    public function update(Request $request, $id){
        $checkBtn = $request->checkBtn;
        $userId = Auth::user()->id;
        $ApplyLeave = ApplyLeave::findOrFail($id);

        // Get order number

        $OrderNum = DB::table('t_validation_process')
        ->select('Order')
        ->where('t_validation_process.StaffApplyLeaveHeaderPKID', $request->PKID)
        ->where('t_validation_process.Status', 1)
        ->first();

        //Get next approving

        $ValidationId = DB::table('t_validation_process')
        ->select('PKID')
        ->where('t_validation_process.StaffApplyLeaveHeaderPKID', $request->PKID)
        ->where('t_validation_process.Status', 1)
        ->first();
        $Validation=ValidationProcess::findOrFail($ValidationId->PKID);

        if($checkBtn){
            $NextValidationId = DB::table('t_validation_process')
            ->select('PKID')
            ->where('t_validation_process.StaffApplyLeaveHeaderPKID', $request->PKID)
            ->where('t_validation_process.Order', $OrderNum->Order+1)
            ->first();
            if($NextValidationId!=null){
                $NextValidation=ValidationProcess::findOrFail($NextValidationId->PKID);
                if($NextValidation !=null){
                    $ApplyLeave->Status = 2;
                    $NextValidation->Status=1;
                    $NextValidation->save();
                }
            }
            else{
                $ApplyLeave->Status = 3;
            }
            $Validation->Status = 3;
        }
        else{
            $ApplyLeave->Status = 4;
            $Validation->Status = 4;
        }
        $ApplyLeave->save();
        $Validation->Comment = $request->comment;
        date_default_timezone_set('Asia/Phnom_Penh');
        $Validation->ApprovedRejectedDate = date('Y-m-d H:i:s');
        $Validation->ApprovedRejectedBy = $userId;
        $Validation->save();
        return response()->json(['message' => 'Your data was approving...']);
    }
}
