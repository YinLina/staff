<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\HolidayModel;
use App\Http\Resources\Holiday\HolidayResource;
use App\Http\Resources\HolidayType\HolidayData;
use Exception;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function read() {
        $holiday = HolidayModel::all();
        return new HolidayResource($holiday);
    }

    public function readHolidayTypeForCombobox() {
        $this->CreateHolidayTypeTemporary();
        $holidayType = DB::table('temp_HolidayType')->get();
        Schema::drop('temp_HolidayType');
        return new HolidayData($holidayType);
    }
    public function create(Request $request) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Description' => 'required',
                'HolidayDate' => 'required',
                'ParameterPKID' => 'required',
            ],[
                'Description.required' => 'Description is required',
                'ParameterPKID.required' => 'Holiday Type field is required',
            ]);
            $holiday = new HolidayModel();
            $holiday->Description = $request->Description;
            $holidayTime = strtotime($request->HolidayDate);
            $holiday->HolidayDate = date('Y-m-d', $holidayTime);
            $holiday->Allowed = $request->Allowed;
            $holiday->HolidayType = $request->ParameterPKID;
            $holiday->Remark = $request->Remark;
            $holiday->save();

            $this->InsertSysLog('Holiday', $userPKID, 'Create', '', 'PK_ID: ' . $holiday->PKID . ' Description: ' . $request->Description, 'Success', '');
            return response()->json(['message' => 'Holiday created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('Holiday', $userPKID, 'Create', '', 'Error Invalid: ' . $request->Description, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'Description' => 'required',
                'HolidayDate' => 'required',
                'ParameterPKID' => 'required',
            ],[
                'Description.required' => 'Description is required',
                'ParameterPKID.required' => 'Holiday Type field is required',
            ]);
            $holiday = HolidayModel::findOrFail($id);
            $oldShiftData = 'PK_ID: ' . $holiday->PKID. ' Description: ' .$holiday->Description;
            $holiday->Description = $request->Description;
            $holidayTime = strtotime($request->HolidayDate);
            $holiday->HolidayDate = date('Y-m-d', $holidayTime);
            $holiday->Allowed = $request->Allowed;     
            $holiday->HolidayType = $request->ParameterPKID;
            $holiday->Remark = $request->Remark;
            $holiday->save();

            $this->InsertSysLog('Holiday', $userPKID, 'Update', $oldShiftData, 'PK_ID: ' . $holiday->PKID . ' Description: ' . $request->Description .' Allowed: '.$request->Allowed, 'Success', '');
            return response()->json(['message' => 'Holiday updated successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('Holiday', $userPKID, 'Update', $oldShiftData, 'PK_ID: ' . $holiday->PKID . ' Error: ' . $request->Description, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete($id) {
        $userPKID = Auth::user()->id;
        $holiday = HolidayModel::findOrFail($id);
        $holiday -> delete();

        $this->InsertSysLog('Holiday', $userPKID, 'Delete', 'PK_ID: ' . $holiday->PKID . ' Description: ' . $holiday->Description, '', 'Success', '');
        return response()->json(['message' => 'Holiday deleted successsfully' ], 200);
    }
}
