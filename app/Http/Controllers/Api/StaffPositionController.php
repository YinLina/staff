<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPosition\StaffPositionResource;
use App\Models\StaffPositionModel;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class StaffPositionController extends Controller
{
    public function read()
    {
        $staffPosition = DB::table('t_staff_position')
            ->join('t_employees', 't_employees.id', '=', 't_staff_position.Employees_id')
            ->join('t_positions', 't_positions.id', '=', 't_staff_position.Positions_id')
            ->select('t_staff_position.PKID', 'Employees_id', 'Positions_id', 'first_name', 'last_name', 'title', 'StartDate', 'EndDate', 't_staff_position.Remark')
            ->get();
        return new StaffPositionResource($staffPosition);
    }

    public function create(Request $request){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Employees_id' => 'required',
                'Positions_id' => 'required',
                'StartDate' => 'required',
            ], [
                'Employees_id.required' => 'Staff Name is required',
            ]);
            $staffPosition = new StaffPositionModel();
            $staffPosition->Employees_id = $request->Employees_id;
            $staffPosition->Positions_id = $request->Positions_id;
            $startTime = strtotime($request->StartDate);
            $staffPosition->StartDate = date('Y-m-d', $startTime);
            $staffPosition->EndDate = $request->EndDate;
            $staffPosition->Remark = $request->Remark;
            $staffPosition->save();

            $this->InsertSysLog('StaffPosition', $userPKID, 'Create', '', 'PK_ID: ' . $staffPosition->PKID . ' Staff: ' . $request->Employees_id, 'Success', '');
            return response()->json(['message' => 'Staff Position created successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('StaffPosition', $userPKID, 'Create', '', 'Staff Error: ' . $request->Emplyees_id. ' Error position, start date!', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Employees_id' => 'required',
                'Positions_id' => 'required',
                'StartDate' => 'required',
            ], [
                'Employees_id.required' => 'Staff Name is required',
            ]
        );
        try {
            $staffPosition = StaffPositionModel::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldStaffPositionData = 'PK_ID: ' . $staffPosition->PKID. ' Staff: ' .$staffPosition->Employees_id;
            $staffPosition->Employees_id = $request->Employees_id;
            $staffPosition->Positions_id = $request->Positions_id;
            $startTime = strtotime($request->StartDate);
            $staffPosition->StartDate = date('Y-m-d', $startTime);
            $staffPosition->EndDate = $request->EndDate;
            $staffPosition->Remark = $request->Remark;
            $staffPosition->save();

            $this->InsertSysLog('StaffPosition', $userPKID, 'Update', $oldStaffPositionData, 'PK_ID: ' . $staffPosition->PKID . ' Staff: ' . $request->Employees_id, 'Success', '');
            return response()->json(['message' => 'Staff Position updated successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('StaffPosition', $userPKID, 'Update','PK_ID: ' . $staffPosition->PKID . ' Staff: ' .$request->Employees_id, 'PK_ID: ' . $staffPosition->PKID . ' Staff Error: ' . $request->Employees_id. ' Error choose fields', 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function delete($id){
        try {
            $userPKID = Auth::user()->id;
            $staffPosition = StaffPositionModel::findOrFail($id);
            $staffPosition->delete();

            $this->InsertSysLog('StaffPosition', $userPKID, 'Delete', 'PK_ID: ' . $staffPosition->PKID . ' Staff: ' . $staffPosition->Employees_id, '', 'Success', '');
            return response()->json(['message' => 'Staff Position deleted successsfully'], 200);
        } 
        catch (Exception $e) {
            $this->InsertSysLog('StaffPosition', $userPKID, 'Delete', 'PK_ID: ' . $staffPosition->PKID . ' Staff Error: ' . $staffPosition->Employees_id. 'Error delete', '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
}
