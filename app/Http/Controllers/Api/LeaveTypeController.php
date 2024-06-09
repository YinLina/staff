<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveType;
use App\Http\Resources\LeaveType\LeaveTypeData;
use Illuminate\Support\Facades\Auth;
use Exception;

class LeaveTypeController extends Controller
{
    public function read() {
        $leaveType = LeaveType::all();
        return new LeaveTypeData($leaveType);
    }

    public function create(Request $request) { 
        try{
            $userPKID = Auth::user()->id;
            $id = DB::table('t_leave_type')->max('PK_ID');
            $this->validate($request, [
                'leaveType' => 'required|string|min:2|max:255|unique:t_leave_type,LeaveType',
            ],[
                'leaveType.required' => 'The leave type name field is required.',
            ]);
            $leaveType = new LeaveType();
            $leaveType->LeaveType = $request->leaveType;
            $leaveType->Allowance = $request->allowance;
            $leaveType->Deduction = $request->deduction;
            $leaveType->Remark = $request->remark;
            $leaveType->PK_ID = $id == null ? 1 : $id + 1;
            $leaveType->save();

            $this->InsertSysLog('leaveTypeSetting',$userPKID,'Create','','PK_ID: '.$leaveType->PK_ID.' Data: '.$request->leaveType,'Success','');
            return response()->json(['message' => 'LeaveType created successfully'], 200);
        }
        catch (Exception $showMessage )
        {
            $this->InsertSysLog('leaveTypeSetting',$userPKID,'Create','','Error: '.$request->leaveType,'Error', $showMessage);
            return response()->json([
                'errors' => $showMessage->errors(),
            ], 400);
        } 
    }

    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'leaveType' => 'required|string|min:2|max:255|unique:t_leave_type,LeaveType,'.$id.',PK_ID',
            ],[
                'leaveType.required' => 'The leave type name field is required.',
            ]);
            $leaveType = LeaveType::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldLeaveTypeData = 'PK_ID: ' . $leaveType->PK_ID. ' LeaveType: ' .$leaveType->LeaveType;
            $leaveType->LeaveType = $request->leaveType;
            $leaveType->Allowance = $request->allowance;
            $leaveType->Deduction = $request->deduction;
            $leaveType->Remark = $request->remark;
            $leaveType->save();

            $this->InsertSysLog('leaveTypeSetting',$userPKID,'Update',$oldLeaveTypeData,'PK_ID: '.$leaveType->PK_ID.' Updated: '.$request->leaveType,'Success','');
            return response()->json(['message' => 'LeaveType created successfully'], 200);
        }
        catch (Exception $showMessage ){
            $this->InsertSysLog('leaveTypeSetting',$userPKID,'Update','PK_ID: '.$leaveType->PK_ID.' Updated: '.$request->leaveType,'Error: '.$request->leaveType. 'Error LeaveType','Error',$showMessage);
            return response()->json([
                'errors' => $showMessage->errors(),
            ], 400);
        } 
    }

    public function delete($id){
        try{
            $userPKID = Auth::user()->id;
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->delete();

            $this->InsertSysLog('leaveTypeSetting',$userPKID,'Delete','PK_ID: '.$leaveType->PK_ID.' Delete: '.$leaveType->LeaveType,'','Success','');
            return response()->json(['message' => 'LeaveType deleted successfully.'], 200);

        } catch (Exception $e) {
            $this->InsertSysLog('leaveTypeSetting', $userPKID, 'Delete', 'PK_ID: ' . $leaveType->PKID . ' Error: ' . $leaveType->LeaveType, '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
}
