<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResignation\StaffResignationResource;
use App\Models\StaffResignationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StaffResignationController extends Controller
{
    public function read() {
        $staffResignation = DB::table('t_staff_resignation')
        ->join('t_employees', 't_employees.id', '=', 't_staff_resignation.Employee_PKID')
        ->join('t_resignation_reason', 't_resignation_reason.PKID', '=', 't_staff_resignation.ResignationReason_PKID')
        ->select('t_staff_resignation.PKID','first_name', 'last_name', 'Employee_PKID', 'ResignationReason_PKID', 
                't_resignation_reason.Description', 't_staff_resignation.Description', 'ResignedDate')
        ->get();
        return new StaffResignationResource($staffResignation);
    }

    public function readResignationReason(){
        $resignationReason = DB::table('t_resignation_reason')
        ->select('PKID', 'Description')
        ->get();
        return response()->json(['resignationReason' => $resignationReason]);
    }

    public function create(Request $request) {
        try{
            $userPKID = Auth::user()->id;

            $this->validate($request, [
                'Employee_PKID' => 'required',
                'ResignationReason_PKID' => 'required',
                'Description' => 'required',
                'ResignedDate' => 'required',
            ],[
                'Employee_PKID.required' => 'Employee name is required',
                'ResignationReason_PKID.required' => 'Resignation reason is required'
            ]);

            $staffResignation = new StaffResignationModel();
            $staffResignation->Employee_PKID = $request->Employee_PKID;
            $staffResignation->ResignationReason_PKID = $request->ResignationReason_PKID;
            $staffResignation->Description = $request->Description;
            $staffResignation->ResignedDate = $request->ResignedDate;
            $staffResignation->save();
            
            $this->InsertSysLog('StaffResignation', $userPKID, 'Create', '', 'PK_ID: ' . $staffResignation->PKID . ' Employee_PKID: ' . $request->Employee_PKID, 'Success', '');
            return response()->json(['message' => 'Staff resignation created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('StaffResignation', $userPKID, 'Create', '', 'StaffResignation Error: ' . $request->Employee_PKID, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;

            $this->validate($request, [
                'Employee_PKID' => 'required',
                'ResignationReason_PKID' => 'required',
                'Description' => 'required',
                'ResignedDate' => 'required',
            ],[
                'Employee_PKID.required' => 'Employee name is required',
                'ResignationReason_PKID.required' => 'Resignation reason is required'
            ]);

            $staffResignation = StaffResignationModel::findOrFail($id);
            $oldStaffResignationData = 'PKID: ' . $staffResignation->PKID. ' Employee_PKID: ' .$staffResignation->Employee_PKID;
            $staffResignation->Employee_PKID = $request->Employee_PKID;
            $staffResignation->ResignationReason_PKID = $request->ResignationReason_PKID;
            $staffResignation->Description = $request->Description;
            $staffResignation->ResignedDate = $request->ResignedDate;
            $staffResignation->save();

            $this->InsertSysLog('StaffResignation', $userPKID, 'Update', $oldStaffResignationData, 'PK_ID: ' . $staffResignation->PKID . ' Employee_PKID: ' . $request->Employee_PKID, 'Success', '');
            return response()->json(['message' => 'Staff resignation created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('StaffResignation', $userPKID, 'Update', $oldStaffResignationData, 'Error: ' . $request->Employee_PKID. ' Error input fields', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete ($id) {
        try {
            $userPKID = Auth::user()->id;
            
            $staffResignation = StaffResignationModel::findOrFail($id);
            $staffResignation->delete();
            return response()->json(['message' => 'Staff resignation deleted successfully.'], 200);
        }catch (Exception $e){
            $this->InsertSysLog('StaffResignation', $userPKID, 'Delete', 'PKID: ' . $staffResignation->PKID . ' StaffResignation Error: ' . $staffResignation->Employee_PKID, '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }  
    }
}