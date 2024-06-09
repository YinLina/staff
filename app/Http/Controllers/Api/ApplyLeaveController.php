<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApplyLeave;
use App\Models\ApplyLeaveDetail;
use App\Models\ValidationProcess;
use App\Models\Group;
use App\Http\Resources\ApplyLeave\ApplyLeaveData;
use App\Models\StaffPositionModel;
use Illuminate\Support\Facades\DB;
use App\Models\WorkflowGroup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
class ApplyLeaveController extends Controller
{
    public function read($lang){
        $this-> CreateValidationStatusTemporary();
        $this-> CreateValidationTypeTemporary();
        $userId = Auth::user()->id;
        $ApplyLeave = DB::table('t_staff_apply_leave_header')
        ->join('t_employees','t_employees.id','=','t_staff_apply_leave_header.StaffPKID')
        ->join('t_users','t_users.employee_id','=','t_employees.id')
        ->join('t_leave_reason','t_leave_reason.PK_ID','=','t_staff_apply_leave_header.LeaveReasonPKID')
        ->join('t_leave_type','t_leave_type.PK_ID','=','t_staff_apply_leave_header.LeaveTypePKID')
        ->join('temp_ValidationStatus','temp_ValidationStatus.StatusId','=','t_staff_apply_leave_header.Status')
        ->join('temp_ValidationType','temp_ValidationType.ValidationTypeId','t_staff_apply_leave_header.ValidationType')
        ->select('first_name','last_name','LeaveReason','LeaveType','t_staff_apply_leave_header.Remark','temp_ValidationStatus.Status','t_staff_apply_leave_header.PKID','LeaveReasonPKID','LeaveTypePKID','StaffPKID','temp_ValidationStatus.StatusId','ValidationTypeId','temp_ValidationType.ValidationType')
        ->where('t_users.id',$userId)
        ->get();
        Schema::drop('temp_ValidationStatus');
        Schema::drop('temp_ValidationType');
        return new ApplyLeaveData($ApplyLeave);
        response()->json(['Data' => $ApplyLeave]);
    }

    public function create(Request $Request){
       
        //----------Modify by Lina #cms-92---------
        try{
            $userPKID = Auth::user()->id;
            $this->validate($Request, [
                'LeaveReasonPKID' => 'required',
                'LeaveTypePKID' => 'required',
                'ParameterPKID' => 'required',
                'DataForm' => 'required'
            ],
            [   
                //Modify by Theara 07/07/23 #CMS-77
                'LeaveReasonPKID.required' => 'The name field is required.',
                'LeaveTypePKID.required' => 'The name field is required.',
                'ParameterPKID.required' => 'The name field is required.',
                'DataForm.required' => 'The field cannot be empty.',
            ]);
            $DateAndTime = $Request->DataForm;
            foreach($DateAndTime as $DateTime)
            {
                $filteredArray = Arr::where($DateAndTime, function ($value, $key) use( $DateTime) {
                    return $value['AttendanceDate'] == $DateTime['AttendanceDate'] && $value['TimeInTimeOutId'] == $DateTime['TimeInTimeOutId'];
                });
                if(count($filteredArray) >= 2){
                    $this->validate($Request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required',
                    ],[
                        'AttendanceDate.required' => 'Data is duplicate.'
                    ]);
                }else if($DateTime['AttendanceDate'] == null || $DateTime['TimeInTimeOutId'] == null){
                    $this->validate($Request, [
                        'AttendanceDate' => 'required',
                        'TimeInTimeOutId' => 'required',
                    ],[
                        'AttendanceDate.required' => 'Data is not empty.'
                    ]);
                }
    
                $Apply = DB::table('t_staff_apply_leave_header')
                    ->join('t_staff_apply_leave_detail'
                    ,'t_staff_apply_leave_header.PKID'
                    ,'=',
                    't_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID')
                    ->where('StaffPKID', $Request->StaffPKID)
                    ->where('ShiftDetailPKID', $DateTime['TimeInTimeOutId'])
                    ->where('AttendanceDate', $DateTime['AttendanceDate'])
                    ->get();
                    if(count($Apply) > 0){
                        $this->validate($Request,[
                            'AttendanceDate' => 'required',
                            'TimeInTimeOutId' => 'required',
                        ],[
                            'AttendanceDate.required' => 'Data is already exist.',
                        ]);
                    }
            }
            $isSubmit = $Request->isSubmit;
            // save button
            $ApplyLeave = new ApplyLeave();
            $ApplyLeave->Remark = $Request->Remark;
            $ApplyLeave->StaffPKID = $Request->StaffPKID;
            $ApplyLeave->LeaveReasonPKID = $Request->LeaveReasonPKID;
            $ApplyLeave->LeaveTypePKID = $Request->LeaveTypePKID;
            $ApplyLeave->ValidationType = $Request->ParameterPKID;
            date_default_timezone_set('Asia/Phnom_Penh');
            $ApplyLeave->CreatedDate = date('Y-m-d H:i:s');
            $ApplyLeave->Status = $isSubmit ? 1 : 0;
            $ApplyLeave->SubmittedDate = $isSubmit ? date('Y-m-d H:i:s') : null;
            $ApplyLeave->save();

            if($isSubmit) {
                $userApplyId = Auth::user()->employee_id;
                $PositionApplyId = DB::table('t_staff_position')
                ->select('Positions_id')
                ->where('Employees_id',$userApplyId)
                ->first();
                $GroupData = DB::table('t_work_flow_group')
                ->join('t_work_flow','t_work_flow.PKID','t_work_flow_group.WorkFlowPKID')
                ->join('t_work_flow_position', 't_work_flow_position.WorkFlowPKID','t_work_flow.PKID')
                ->select('GroupPKID','FlowOrder')
                ->where('ValidationType',$ApplyLeave->ValidationType)
                ->where('PositionPKID',$PositionApplyId->Positions_id)
                ->get();
                if(count($GroupData) > 0){
                    foreach($GroupData as $S){
                        $validation = new ValidationProcess();
                        $validation->StaffApplyLeaveHeaderPKID = $ApplyLeave->PKID;
                        $validation->GroupPKID = $S->GroupPKID;
                        $validation->Order = $S->FlowOrder;
                        $validation->Status = $S->FlowOrder == 1 ? 1 : 0;
                        $validation->save();
                    }
                }else{
                    $ApplyLeave->Status = 3;
                    $ApplyLeave->save();
                }
            }
            //$DateAndTime = $Request->DataForm;
            foreach($DateAndTime as $DateTime){
                $ApplyLeaveDetail = new ApplyLeaveDetail();
                $ApplyLeaveDetail->StaffApplyLeaveHeaderPKID = $ApplyLeave->PKID;
                $ApplyLeaveDetail->AttendanceDate = $DateTime['AttendanceDate'];
                $ApplyLeaveDetail->ShiftDetailPKID = $DateTime['TimeInTimeOutId'];
                $ApplyLeaveDetail->save();
            }
    
            $this->InsertSysLog('ApplyLeave', $userPKID, 'Create', '', 'PK_ID: '.$ApplyLeave->PKID .' Staff ApplyLeave: ' .$Request->StaffName , 'Success', '');
            return response()->json(['message' => 'Your data was successfully saved.', 200]);
        }catch(Exception $e){
            $this->InsertSysLog('ApplyLeave', $userPKID, 'Create', '', 'ApplyLeave Error: ' . $Request->StaffName, 'Error', $e);
            return response()->json([
                'errors' => $e->getMessage(),
            ], 400);
        }
    }


    public function update(Request $request, $id) {
        //----------Modify by Lina #cms-92--------
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'LeaveReasonPKID' => 'required',
                'LeaveTypePKID' => 'required',
                'ParameterPKID' => 'required',
                'DataForm' => 'required',
            ],[
                'LeaveReasonPKID.required' => 'The name field is required.',
                'LeaveTypePKID.required' => 'The name field is required.',
                'ParameterPKID.required' => 'The name field is required.',
                'DataForm.required' => 'The name field is required.',
            ]);
                $DateAndTime = $request->DataForm;
                foreach($DateAndTime as $Data){
                    $filteredArray = Arr::where($DateAndTime, function($value,$key) use($Data){
                        return $value['AttendanceDate'] == $Data['AttendanceDate'] && $value['TimeInTimeOutId'] == $Data['TimeInTimeOutId'];
                    });
                    if(count($filteredArray) >= 2){
                        $this->validate($request,[
                            'AttendanceDate' => 'required',
                            'TimeInTimeOutId' => 'required'
                        ],[
                            'AttendanceDate.required' => 'Data is duplicated.'
                        ]);
                    } //Modify by Theara 07/07/23 #CMS-77
                    // else if(count($filteredArray) == 1){
                    //     $this->validate($request,[
                    //         'AttendanceDate' => 'required',
                    //         'TimeInTimeOutId' => 'required'
                    //     ],[
                    //         'AttendanceDate.required' => 'Data is already exist.'
                    //     ]);
                    // }
                    if($Data['AttendanceDate'] == null || $Data['TimeInTimeOutId'] == null){
                        $this->validate($request,[
                            'AttendanceDate' => 'required',
                            'TimeInTimeOutId' => 'required'
                        ],[
                            'AttendanceDate.required' => 'Data in not empty.'
                        ]);
                    }
                }
                $isSubmit = $request->isSubmit;
                $ApplyLeave = ApplyLeave::findOrFail($id);
                // $oldApplyLeaveData = 'PK_ID: '.$ApplyLeave->PKID .' ApplyLeave: ' .$ApplyLeave->StaffPKID. ' ParmeterID: '.$ApplyLeave->ParameterPKID;
                $ApplyLeave->Remark = $request->Remark;
                $ApplyLeave->StaffPKID = $request->StaffPKID;
                $ApplyLeave->LeaveReasonPKID = $request->LeaveReasonPKID;
                $ApplyLeave->LeaveTypePKID = $request->LeaveTypePKID;
                $ApplyLeave->ValidationType = $request->ParameterPKID;
                date_default_timezone_set("Asia/Phnom_Penh");
                $ApplyLeave->SubmittedDate = $isSubmit ? date('Y-m-d H:i:s') : null;
                $ApplyLeave->Status = $isSubmit ? 1 : 0;
                $ApplyLeave->save();
                DB::delete('DELETE FROM t_staff_apply_leave_detail WHERE StaffApplyLeaveHeaderPKID = '. $id);
    
                foreach($DateAndTime as $DateTime)
                {
                    $ApplyLeaveDetail = new ApplyLeaveDetail();
                    $ApplyLeaveDetail->StaffApplyLeaveHeaderPKID = $ApplyLeave->PKID;
                    $ApplyLeaveDetail->AttendanceDate = $DateTime['AttendanceDate'];
                    $ApplyLeaveDetail->ShiftDetailPKID = $DateTime['TimeInTimeOutId'];
                    $ApplyLeaveDetail->save();
                }
                if($isSubmit == true){
                    $userApplyId = Auth::user()->employee_id;
                    $PositionApplyId = DB::table('t_staff_position')
                    ->select('Positions_id')
                    ->where('Employees_id',$userApplyId)
                    ->first();
                    DB::delete('DELETE FROM t_validation_process WHERE StaffApplyLeaveHeaderPKID = '. $id);
                    $GroupData = DB::table('t_work_flow_group')
                    ->join('t_work_flow','t_work_flow.PKID','t_work_flow_group.WorkFlowPKID')
                    ->join('t_work_flow_position','t_work_flow_position.WorkFlowPKID','t_work_flow.PKID')
                    ->select('GroupPKID','FlowOrder')
                    ->where('ValidationType',$ApplyLeave->ValidationType)
                    ->where('PositionPKID', $PositionApplyId->Positions_id)
                    ->get();
                    if(count($GroupData) > 0){
                        foreach ($GroupData as $Data)
                        {
                            $validation = new ValidationProcess();
                            $validation->StaffApplyLeaveHeaderPKID = $ApplyLeave->PKID;
                            $validation->GroupPKID = $Data->GroupPKID;
                            $validation->Order = $Data->FlowOrder;
                            $validation->Status =$Data->FlowOrder == 1 ? 1 : 0;
                            $validation->save();
                        }
                    }else{
                        $ApplyLeave->Status = 3;
                        $ApplyLeave->save();
                    }
                    
                }
    
            $this->InsertSysLog('ApplyLeave', $userPKID, 'Update', 'PK_ID: '.$ApplyLeave->PKID .' Staff: ' .$request->StaffName, 'PK_ID: '.$ApplyLeave->PKID .' ,ParameterID: ' .$request->ParameterPKID .' ,LeaveReasonID: ' .$request->LeaveReasonPKID , 'Success', '');
            return response()->json(['message' => 'Your data was submited successfully.'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('ApplyLeave', $userPKID, 'Update', 'PK_ID: '.$ApplyLeave->PKID .' Staff: ' .$request->StaffName, 'ApplyLeave Error: ' . $request->StaffName, 'Error', $e);
            return response()->json([
                'errors' => $e->getMessage(),
            ], 400);
        }
        
    }

    public function delete($id) {
        try{
            $userPKID = Auth::user()->id;
            $ApplyLeave = ApplyLeave::findOrFail($id);
            DB::delete('DELETE FROM t_staff_apply_leave_detail WHERE StaffApplyLeaveHeaderPKID = '. $id);
            DB::delete('DELETE FROM t_validation_process WHERE StaffApplyLeaveHeaderPKID ='. $id);
            $ApplyLeave->delete();

            $this->InsertSysLog('ApplyLeave', $userPKID, 'Delete', 'PK_ID: '.$ApplyLeave->PKID .' Staff: ' .$ApplyLeave->StaffPKID, '', 'Success', '');
            return response()->json(['message' => 'Your data was deleted successfully.'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('ApplyLeave', $userPKID, 'Delete', 'PK_ID: ' . $ApplyLeave->PKID . ' Department Error: ' . $ApplyLeave->StaffPKID, '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
}
