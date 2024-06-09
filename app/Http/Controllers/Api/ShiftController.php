<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftModel;
use App\Models\ShiftDetail;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Shift\ShiftResource;
use App\Http\Resources\Shift\ShiftHeaderResource;
use App\Http\Resources\ShiftDetail\ShiftDetailResource;
use Illuminate\Support\Arr;

class ShiftController extends Controller
{
    public function read($employee) {
        $shift = DB::table('t_shift_header')
        ->join('t_shift_detail', 't_shift_detail.ShiftHeaderPKID', '=', 't_shift_header.PKID')
        ->join('t_staff_shift','t_staff_shift.ShiftHeader_PKID','t_shift_header.PKID')
        ->join('t_employees','t_employees.id','t_staff_shift.Employees_id')
        ->where('t_employees.id',$employee)
        ->select('t_staff_shift.Employees_id','t_shift_detail.TimeIn','t_shift_detail.TimeOut','t_shift_detail.PKID','t_shift_header.ShiftCode','t_shift_header.ShiftName','t_staff_shift.StartDate','t_staff_shift.EndDate')
        ->get();
        // return new ShiftResource($shift);
        // $shift = ShiftModel::with('shiftDetail')->get();
        return new ShiftResource($shift);
    }
    public function readShift(){
        $getshift = ShiftModel::all();
        return new ShiftHeaderResource($getshift);
    }
    public function readShiftDetail() {
        $shift = DB::table('t_shift_header')->join('t_shift_detail','t_shift_detail.ShiftHeaderPKID', '=','t_shift_header.PKID')->get();
        return new ShiftDetailResource($shift);
    }

    public function create(Request $request) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'ShiftCode' => 'required|string|min:2|max:255|unique:t_shift_header,ShiftCode',
                'shiftdetailFrom' => 'required',
            ],[
                'ShiftCode.required' => 'The shift code field is required.',
                'shiftdetailFrom.required' => 'Shift detail field is required.',
            ]);
            // Modify by Huychoung 12/09/23 #88
            $SectionAndTime = $request->shiftdetailFrom;
            foreach ($SectionAndTime as $DateTime) {
                $filteredArray = Arr::where($SectionAndTime, function($value, $key) use ($DateTime) {
                    return $value['Section'] == $DateTime['Section'] && $value['TimeIn'] == $DateTime['TimeIn'] && $value['TimeOut'] == $DateTime['TimeOut'];
                });
                if(count($filteredArray) >= 2){
                    $this->validate($request, [
                        'Section' => 'required',
                        'TimeTn' => 'required',
                        'TimeOut' => 'required',
                    ],[
                        'Section.required' => 'Data is duplicate.'
                    ]);
                }
                else if($DateTime['Section'] == null || $DateTime['TimeIn'] == null || $DateTime['TimeOut'] == null){
                    $this->validate($request, [
                        'Section' => 'required',
                        'TimeIn' => 'required',
                        'TimeOut' => 'required',
                    ],[
                        'Section.required' => 'Data is not empty.'
                    ]);
                }   
            }
            $shift = new ShiftModel();
            $shift->ShiftCode = $request->ShiftCode;
            $shift->ShiftName = $request->ShiftName;
            $shift->Remark = $request->Remark;
            $shift->save();
            $tempShiftDetail = $request->shiftdetailFrom;
            foreach($tempShiftDetail as $key => $element) {
                $shiftdetailFrom = new ShiftDetail();
                $shiftdetailFrom->ShiftHeaderPKID = $shift->PKID;
                $shiftdetailFrom->Section = $element["Section"];
                $strTimeIn = self::convertFormTimeToTimeDB($element["TimeIn"]);
                $strTimeOut = self::convertFormTimeToTimeDB($element["TimeOut"]);
                $timeIn = date_create_from_format('H:i', $strTimeIn);
                $timeOut= date_create_from_format('H:i', $strTimeOut);
                $shiftdetailFrom->TimeIn = $timeIn;
                $shiftdetailFrom->TimeOut = $timeOut;
                $shiftdetailFrom->Remark = $element["Remark"];
                $shiftdetailFrom->save();
            }
            $this->InsertSysLog('shift', $userPKID, 'Create', '', 'PK_ID: ' . $shift->PKID . ' Shift: ' . $request->ShiftName, 'Success', '');
            return response()->json(['message' => 'Shift created successfully'], 200);

        }catch(Exception $e){
            $this->InsertSysLog('shift', $userPKID, 'Create', '', 'Shift Error: ' . $request->ShiftName, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;    
            $this->validate($request, [
                'ShiftCode' => 'required|string|min:2|max:255|unique:t_shift_header,ShiftCode,'.$id.',PKID',
                'shiftdetailFrom' => 'required',
            ],
            [
                'ShiftCode.required' => 'The shift code field is required.',
                'shiftdetailFrom.required' => 'Shift detail field is required.',
            ]);
            // Modify by Huychoung 12/09/23 #88
            $SectionAndTime = $request->shiftdetailFrom;
            foreach ($SectionAndTime as $DateTime) {
                $filteredArray = Arr::where($SectionAndTime, function($value, $key) use ($DateTime) {
                    return $value['Section'] == $DateTime['Section'] && $value['TimeIn'] == $DateTime['TimeIn'] && $value['TimeOut'] == $DateTime['TimeOut'];
                });
                if(count($filteredArray) >= 2){
                    $this->validate($request, [
                        'Section' => 'required',
                        'TimeTn' => 'required',
                        'TimeOut' => 'required',
                    ],[
                        'Section.required' => 'Data is duplicate.'
                    ]);
                }
                else if($DateTime['Section'] == null || $DateTime['TimeIn'] == null){
                    $this->validate($request, [
                        'Section' => 'required',
                        'TimeIn' => 'required',
                        'TimeOut' => 'required',
                    ],[
                        'Section.required' => 'Data is not empty.'
                    ]);
                }   
            }
            $shift = ShiftModel::findOrFail($id);
            $oldShiftData = 'PK_ID: ' . $shift->PKID. ' Shift: ' .$shift->ShiftName;
            $shift->ShiftCode = $request->ShiftCode;
            $shift->ShiftName = $request->ShiftName;
            $shift->Remark = $request->Remark;
            $shift->save();
        
            DB::delete('DELETE FROM t_shift_detail WHERE ShiftHeaderPKID = ' .$id);
            $tempShiftDetail = $request->shiftdetailFrom;
            foreach($tempShiftDetail as $key => $element) {
                $shiftdetailFrom = new ShiftDetail();
                $shiftdetailFrom->ShiftHeaderPKID = $shift->PKID;
                $shiftdetailFrom->Section = $element["Section"];
                $strTimeIn = self::convertFormTimeToTimeDB($element["TimeIn"]);
                $strTimeOut = self::convertFormTimeToTimeDB($element["TimeOut"]);
                $timeIn = date_create_from_format('H:i', $strTimeIn);
                $timeOut= date_create_from_format('H:i', $strTimeOut);
                $shiftdetailFrom->TimeIn = $timeIn;
                $shiftdetailFrom->TimeOut = $timeOut;
                $shiftdetailFrom->Remark = $element["Remark"];
                $shiftdetailFrom->save();
            }

            $this->InsertSysLog('shift', $userPKID, 'Update', $oldShiftData, 'PK_ID: ' . $shift->PKID . ' Shift: ' . $request->ShiftName, 'Success', '');
            return response()->json(['message' => 'Shift update successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('shift', $userPKID, 'Update', $oldShiftData, 'PK_ID: ' . $shift->PKID . ' Shift Error: ' . $request->ShiftName, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete($id){
        $userPKID = Auth::user()->id;
        $shift = ShiftModel::findOrFail($id);
        $shift->delete();
        DB::delete('DELETE FROM t_shift_detail WHERE ShiftHeaderPKID = ' .$id);

        $this->InsertSysLog('shift', $userPKID, 'Delete', 'PK_ID: ' . $shift->PKID . ' Shift: ' . $shift->ShiftName, '', 'Success', '');
        return response()->json(['message' => 'Shift deleted successfully.'], 200);
    }
    
    public static function convertFormTimeToTimeDB($timeParam) {
        $timeDB = "";
        $intTimeHour = 0;
        $strTimeHour = explode(":", $timeParam)[0];
        $strTimeMinute = explode(":", $timeParam)[1];
        if(explode(" ", $timeParam)[1] == "PM") {
            if($strTimeHour == 12) {
                $intTimeHour = 12;
            } else {
                $intTimeHour = 12 + (int)$strTimeHour;
            }
        } else {
            if($strTimeHour == 12) {
                $intTimeHour = 00;
            } else {
                $intTimeHour = (int)$strTimeHour;
            }
        }
        $timeDB = $intTimeHour .":". $strTimeMinute;
        return explode(" ", $timeDB)[0];
    }
};