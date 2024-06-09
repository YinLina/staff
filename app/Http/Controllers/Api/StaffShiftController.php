<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffShift\StaffShiftResource;
use App\Models\StaffShiftModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class StaffShiftController extends Controller
{
    public function read()
    {   
        $staffShift = DB::table('t_staff_shift')
            ->join('t_employees', 't_employees.id', '=', 't_staff_shift.Employees_id')
            ->join('t_shift_header', 't_shift_header.PKID', '=', 't_staff_shift.ShiftHeader_PKID')
            // ->join('t_shift_detail', 't_shift_detail.ShiftHeaderPKID', '=', 't_staff_shift.ShiftHeader_PKID')
            ->select('t_staff_shift.PKID', 'Employees_id', 'ShiftHeader_PKID', 'StartDate', 'EndDate', 't_staff_shift.Remark',
                    't_employees.first_name', 't_employees.last_name', 't_shift_header.ShiftName')
            ->get();
        return new StaffShiftResource($staffShift);
    }

    public function readShift(){
        $getShift = DB::table('t_shift_header')
        ->select('PKID', 'ShiftName')
        ->get();
        return response()->json(['shift' => $getShift]);
    }

    // public function updateStaffShift(Request $request){
    //     $staffShift = $request->StaffShiftDataForm;
    //     $employee_pkid = $request->Employees_id;
    //     StaffShiftModel::where('Employees_id', $employee_pkid)->delete();
    //     foreach($staffShift as $key => $element){
    //         $staffShift = new StaffShiftModel();
    //         $staffShift->Employees_id = $employee_pkid;
    //         $staffShift->ShiftHeader_PKID = $element["ShiftHeader_PKID"];
    //         $staffShift->StartDate = $element["StartDate"];
    //         $staffShift->EndDate = $element["EndDate"];
    //         $staffShift->Remark = $element["Remark"];
    //         $staffShift->save();
    //     }
    //     return response()->json(['message' => 'Employee reference save successfully'], 200);
    // }

    public function create(Request $request){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Employees_id' => 'required',
                'ShiftHeader_PKID' => 'required',
                'StartDate' => 'required',
            ], [
                'Employees_id.required' => 'Staff Name is required',
                'ShiftHeader_PKID.required' => 'Staff Shift is required',
            ]);
            $staffShift = new StaffShiftModel();
            $staffShift->Employees_id = $request->Employees_id;
            $staffShift->ShiftHeader_PKID = $request->ShiftHeader_PKID;
            $staffShift->StartDate = $request->StartDate;
            $staffShift->EndDate = $request->EndDate;
            $staffShift->Remark = $request->Remark;
            $staffShift->save();

            $this->InsertSysLog('StaffShift', $userPKID, 'Create', '', 'PK_ID: ' . $staffShift->PKID . ' Staff Shift: ' . $request->Employees_id, 'Success', '');
            return response()->json(['message' => 'Staff Shift created successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('StaffShift', $userPKID, 'Create', '', 'StaffShift Error: ' . $request->Emplyees_id. ' Error ', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Employees_id' => 'required',
                'ShiftHeader_PKID' => 'required',
                'StartDate' => 'required',
            ], [
                'Employees_id.required' => 'Staff Name is required',
                'ShiftHeader_PKID.required' => 'Staff Shift is required',
            ]);
            $staffShift = StaffShiftModel::findOrFail($id);
            $oldStaffShiftData = 'PK_ID: ' . $staffShift->PKID. ' Staff: ' .$staffShift->Employees_id;
            $staffShift = new StaffShiftModel();
            $staffShift->Employees_id = $request->Employees_id;
            $staffShift->ShiftHeader_PKID = $request->ShiftHeader_PKID;
            $startTime = strtotime($request->StartDate);
            $staffShift->StartDate = date('Y-m-d', $startTime);
            $staffShift->EndDate = $request->EndDate;
            $staffShift->Remark = $request->Remark;
            $staffShift->save();

            $this->InsertSysLog('StaffShift', $userPKID, 'Update', $oldStaffShiftData, 'PK_ID: ' . $staffShift->PKID . ' Staff Shift: ' . $request->Employees_id, 'Success', '');
            return response()->json(['message' => 'Staff Shift updated successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('StaffShift', $userPKID, 'Update','PK_ID: ' . $staffShift->PKID . ' Staff Shift: ' .$request->Employees_id, 'PK_ID: ' . $staffShift->PKID . ' Staff Shift Error: ' . $request->Employees_id. ' Not found', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete (Request $request) {
        $selectIds = $request->selectedIds;
        foreach($selectIds as $id) {
            $staffShift = StaffShiftModel::findOrFail($id);
            $staffShift->delete();
        }
        return response()->json(['message' => 'Staff Shift deleted successfully.'], 200);
    }
}
