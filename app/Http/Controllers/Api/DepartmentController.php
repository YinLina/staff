<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepartmentModel;
use App\Http\Resources\Department\DepartmentResources;
use App\Http\Resources\Department\ParentDepartmentResources;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Exception;

class DepartmentController extends Controller
{
    // Modified by Huychoung 04/08/2023 #84
    public function read()
    {
        $department = DepartmentModel::all();
        return new DepartmentResources($department);
    }

    public function create(Request $request){
        $this->validate($request, 
            [
                'Department' => 'required|unique:t_department,Department',
            ],
            [
                'Department.required' => 'The department name field is required.',
                'Department.unique' => 'This department is already existed.'
        ]);
        //----------Modify by Lina #cms-92---------
        try {
            $userPKID = Auth::user()->id;
            $department = new DepartmentModel();
            $department->Department = $request->Department;
            $department->ParentDepartment_PKID = $request->ParentDepartment_PKID;
            $department->Remark = $request->Remark;
            $department->save();

            $this->InsertSysLog('departmentSetting', $userPKID, 'Create', '', 'PK_ID: ' . $department->PKID . ' Department: ' . $request->Department, 'Success', '');
            return response()->json(['message' => 'Department created successfully'], 200);
        }catch (Exception $showMessage) {
            $this->InsertSysLog('departmentSetting',$userPKID,'Create','','Error: '.$request->Department,'Error', $showMessage);
            return response()->json([
                'Error' => $showMessage->getMessage(),
            ], 400);
        } 
    }

    public function update(Request $request, $id){
        $this->validate($request,[
                'Department' => 'required',
            ],
            [
                'Department.required' => 'The Department name field is required.'
        ]);
        //----------Modify by Lina #cms-92---------
        try {
            $userPKID = Auth::user()->id;
            $department = DepartmentModel::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldDepartmentData = 'PK_ID: ' . $department->PKID. ' Department: ' .$department->Department;
            $department->Department = $request->Department;
            $department->ParentDepartment_PKID = $request->ParentDepartment_PKID;
            $department->Remark = $request->Remark;
            $department->save();

            $this->InsertSysLog('departmentSetting', $userPKID, 'Update', $oldDepartmentData, 'PK_ID: ' . $department->PKID . ' Department: ' . $request->Department, 'Success', '');
            return response()->json(['message' => 'Department created successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('departmentSetting', $userPKID, 'Update',$oldDepartmentData, 'PK_ID: ' . $department->id . ' Department Error: ' . $request->Department, 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function delete($id){
        try {
            $userPKID = Auth::user()->id;
            $department = DepartmentModel::findOrFail($id);
            $parentDepartments = DB::table('t_department')
                ->where('ParentDepartment_PKID', $department->PKID)
                ->get();
            $stringMessage = '';
            if (count($parentDepartments) > 0) {
                $stringMessage .= 'Parent Department;';
            }
            $positionDepartments = DB::table('t_positions')
                ->where('Department_PKID', $department->PKID)
                ->get();
            if (count($positionDepartments) > 0) {
                $stringMessage = $stringMessage . 'Position';
            }
            if ($stringMessage != '') {
                return response()->json(['errorMessage' => $stringMessage], 200);
            }
            $department->delete();

            $this->InsertSysLog('departmentSetting', $userPKID, 'Delete', 'PK_ID: ' . $department->PKID . ' Department: ' . $department->Department, '', 'Success', '');
            return response()->json(['message' => 'Department deleted successfully.'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('departmentSetting', $userPKID, 'Delete', 'PK_ID: ' . $department->PKID . ' Department Error: ' . $department->Department, '', 'Error',$e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }
}
