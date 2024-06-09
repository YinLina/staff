<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LeaveReasonModels;
use App\Http\Resources\LeaveReason\LeaveReasonData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LeaveReasonController extends Controller
{
    public function read() {
        $leaveReason = LeaveReasonModels::all();
        return new LeaveReasonData($leaveReason);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'leaveReason' => 'required|string|min:2|max:255|unique:t_leave_reason,LeaveReason',
        ],[
            'leaveReason.required' => 'The leave reason is required',
        ]);
        try{
            $userPKID = Auth::user()->id;
            $id = DB::table('t_leave_reason')->max('PK_ID');
            $leaveReason = new leaveReasonModels();
            $leaveReason->LeaveReason = $request->leaveReason;
            $leaveReason->Remark = $request->remark;
            $leaveReason->PK_ID = $id == null ? 1 : $id + 1;
            $leaveReason->save();
            
            $this->InsertSysLog('LeaveReson', $userPKID, 'Create', '', 'PK_ID: ' . $leaveReason->PK_ID . ' LeaveReason: ' . $request->leaveReason, 'Success', '');
            return response()->json(['message' => 'Leave reason created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('LeaveReson', $userPKID, 'Create', '', 'LeaveReson Error: ' . $request->leaveReason, 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'leaveReason' => 'required|string|min:2|unique:t_leave_reason,LeaveReason,'.$id.',PK_ID',
        ],[
            'leaveReason.required' => 'The leave reason is required',
        ]);
        try{
            $userPKID = Auth::user()->id;
            $leaveReason = LeaveReasonModels::findOrFail($id);
            $oldLeaveReasonData = 'PK_ID: ' . $leaveReason->PK_ID. ' LeaveReson: ' .$leaveReason->LeaveReason;
            $leaveReason->LeaveReason = $request->leaveReason;
            $leaveReason->Remark = $request->remark;
            $leaveReason->save();

            $this->InsertSysLog('LeaveReson', $userPKID, 'Update', $oldLeaveReasonData, 'PK_ID: ' . $leaveReason->PK_ID . ' LeaveReason: ' . $request->leaveReason, 'Success', '');
            return response()->json(['message' => 'Leave reason created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('LeaveReson', $userPKID, 'Update', $oldLeaveReasonData, 'Error: ' . $request->leaveReason. ' Error input fields', 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
        
    }

    public function delete($id) {
        $userPKID = Auth::user()->id;
        $leaveReason = LeaveReasonModels::findOrFail($id);
        $leaveReason -> delete();

        $this->InsertSysLog('LeaveReson', $userPKID, 'Delete', 'PK_ID: ' . $leaveReason->PK_ID . ' Delete LeaveReason: ' . $leaveReason->LeaveReason, '', 'Success', '');
        return response()->json(['message' => 'Leave reason deleted successsfully'], 200);
    }
}
