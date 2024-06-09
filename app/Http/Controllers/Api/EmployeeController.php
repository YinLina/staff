<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\Employee\employeeData;
use App\Http\Resources\AddressProvince\ProvinceResource;
use App\Http\Resources\Children\EmployeeChildrenResource;
use App\Http\Resources\Education\EmployeeEducationResource;
use App\Http\Resources\EmployeeLanguage\EmployeeLanguageResource;
use App\Http\Resources\Parameter\ParameterResource;
use App\Models\Employee;
use App\Models\EmployeeChildrenModel;
use App\Models\EmployeeEducationModel;
use App\Models\EmployeeExperienceModel;
use App\Models\EmployeeLanguageModel;
use App\Models\EmployeeOtherSkillModel;
use App\Models\EmployeeReferienceModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function active()
    {
        return new employeeData(Employee::with('position', 'shift')->where('is_inactive', false)->get());
    }

    public function inactive()
    {
        return new employeeData(Employee::with('position', 'shift')->where('is_inactive', true)->get());
    }

    // modify LINA 02/08/2023 #cms-80 (select change data)
    public function readProvinceForm()
    {
        $this->CreateAddressProvinceTemporary();
        $province = DB::table('temp_AddressProvince')
                  ->get();
        Schema::drop('temp_AddressProvince');
        return new ProvinceResource($province);
    }
    public function readDistrictByProvinceCode($id)
    {
        $getProvinceCodes = DB::table('t_address_province')
            ->select('ProvinceCode')
            ->where('PKID', $id)
            ->first();
        $districtListByProvinceCode = DB::table('t_address_district')
            ->select('DistrictCode', 'DistrictName')
            ->where('ProvinceCode', $getProvinceCodes->ProvinceCode)
            ->get();
        return response()->json(['districtListByProvinceCode' => $districtListByProvinceCode]);
    }
    public function readCommuneByDistrictCode($getDistrictCodes)
    {
        $getDistrictCodes = DB::table('t_address_district')
            ->select('DistrictCode')
            ->where('DistrictCode', $getDistrictCodes)
            ->first();
        $communeListByDistrictCode = DB::table('t_address_commune')
            ->select('CommuneCode', 'CommuneName')
            ->where('DistrictCode', $getDistrictCodes->DistrictCode)
            ->get();
        return response()->json(['communeListByDistrictCode' => $communeListByDistrictCode]);
    }
    public function readCommuneByVillageCode($getCommuneCodes)
    {
        $getCommuneCodes = DB::table('t_address_commune')
            ->select('CommuneCode')
            ->where('CommuneCode', $getCommuneCodes)
            ->first();
        $villageListByCommuneCode = DB::table('t_address_village')
            ->select('VillageCode', 'VillageName')
            ->where('CommuneCode', $getCommuneCodes->CommuneCode)
            ->get();
        return response()->json(['villageListByCommuneCode' => $villageListByCommuneCode]);
    }

    public static function readChildrenList($id){
        $childrenList = DB::table('t_employee_children')
        ->select('PKID', 'Employee_PKID', 'FullName', 'Gender', 'DateOfBirth', 'Remark')
        ->where('Employee_PKID', $id)
        ->get();
        return $childrenList;
    }
    public static function readEducationList($id){
        $educationList = DB::table('t_employee_education')
        ->select('PKID', 'Employee_PKID', 'SchoolName', 'Address', 'StartDate', 'EndDate', 'Skill', 'Detail')
        ->where('Employee_PKID', $id)
        ->get();
        return $educationList;
    }
    public static function readOtherSkillList($id){
        $educationList = DB::table('t_employee_other_skills')
        ->select('PKID', 'Employee_PKID', 'TrainingPlace', 'Address', 'StartDate', 'EndDate', 'Skill', 'Detail')
        ->where('Employee_PKID', $id)
        ->get();
        return $educationList;
    }
    public static function readExperienceList($id){
        $experienceList = DB::table('t_employee_experience')
        ->select('PKID', 'Employee_PKID', 'Company', 'Address', 'StartDate', 'EndDate', 'Position', 'Detail')
        ->where('Employee_PKID', $id)
        ->get();
        return $experienceList;
    }
    public static function readLanguageList($id){
        $languageList = DB::table('t_employee_languages')
        ->select('PKID', 'Employee_PKID', 'Language', 'Level', 'Detail')
        ->where('Employee_PKID', $id)
        ->get();
        return $languageList;
    }
    public static function readReferenceList($id){
        $referenceList = DB::table('t_employee_reference')
        ->select('PKID', 'Employee_PKID', 'FullName', 'Position', 'Company', 'Email', 'PhoneNumber')
        ->where('Employee_PKID', $id)
        ->get();
        return $referenceList;
    }

    public function createOrUpdateEmpChild(Request $request){
        $childList = $request->DataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeChildrenModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($childList as $key => $element){
            $children = new EmployeeChildrenModel();
            $children->Employee_PKID = $employee_pkid;
            $children->FullName = $element["FullName"];
            $children->Gender = $element["Gender"];
            $children->DateOfBirth = $element['DateOfBirth'];
            $children->Remark = $element["Remark"];
            $children->save();
        }
        return response()->json(['message' => 'Employee children save successfully'], 200);
    }
    public function createOrUpdateEmpEducation(Request $request){
        $education = $request->EducationDataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeEducationModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($education as $key => $element){
            $education = new EmployeeEducationModel();
            $education->Employee_PKID = $employee_pkid;
            $education->SchoolName = $element["SchoolName"];
            $education->Address = $element["Address"];
            $education->StartDate = $element['StartDate'];
            $education->EndDate = $element['EndDate'];
            $education->Skill = $element["Skill"];
            $education->Detail = $element["Detail"];
            $education->save();
        }
        return response()->json(['message' => 'Employee education save successfully'], 200);
    }
    public function createOrUpdateEmpOtherSkill(Request $request){
        $otherSkill = $request->OtherSkillDataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeOtherSkillModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($otherSkill as $key => $element){
            $otherSkill = new EmployeeOtherSkillModel();
            $otherSkill->Employee_PKID = $employee_pkid;
            $otherSkill->TrainingPlace = $element["TrainingPlace"];
            $otherSkill->Address = $element["AddressOtherSkill"];
            $otherSkill->StartDate = $element['StartDateOtherSkill'];
            $otherSkill->EndDate = $element['EndDateOtherSkill'];
            $otherSkill->Skill = $element["Skill"];
            $otherSkill->Detail = $element["Detail"];
            $otherSkill->save();
        }
        return response()->json(['message' => 'Employee other skill save successfully'], 200);
    }
    public function createOrUpdateEmpExperience(Request $request){
        $experience = $request->ExperienceDataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeExperienceModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($experience as $key => $element){
            $experience = new EmployeeExperienceModel();
            $experience->Employee_PKID = $employee_pkid;
            $experience->Company = $element["Company"];
            $experience->Address = $element["AddressExperience"];
            $experience->StartDate = $element["StartDateExperience"];
            $experience->EndDate = $element["EndDateExperience"];
            $experience->Position = $element["Position"];
            $experience->Detail = $element["Detail"];
            $experience->save();
        }
        return response()->json(['message' => 'Employee experience save successfully'], 200);
    }
    public function createOrUpdateEmpLanguage(Request $request){
        $language = $request->LanguageDataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeLanguageModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($language as $key => $element){
            $language = new EmployeeLanguageModel();
            $language->Employee_PKID = $employee_pkid;
            $language->Language = $element["Language"];
            $language->Level = $element["Level"];
            $language->Detail = $element["Detail"];
            $language->save();
        }
        return response()->json(['message' => 'Employee language save successfully'], 200);
    }
    public function createOrUpdateEmpReference(Request $request){
        $reference = $request->ReferenceDataForm;
        $employee_pkid = $request->Employee_PKID;
        EmployeeReferienceModel::where('Employee_PKID', $employee_pkid)->delete();
        foreach($reference as $key => $element){
            $reference = new EmployeeReferienceModel();
            $reference->Employee_PKID = $employee_pkid;
            $reference->FullName = $element["FullName"];
            $reference->Position = $element["Position"];
            $reference->Company = $element["Company"];
            $reference->Email = $element["Email"];
            $reference->PhoneNumber = $element["PhoneNumber"];
            $reference->save();
        }
        return response()->json(['message' => 'Employee reference save successfully'], 200);
    }

    public function create(Request $request){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'firstName' => 'required|string|min:2|max:100',
                'lastName' => 'required|string|min:2|max:100',
                'email' => 'required|email|max:255|regex:/(.*)\.com/i|unique:t_employees,email',
                'gender' => 'required',
                'start_date' => 'required',
                'role_id' => 'required',
                // 'position_id' => 'required',
                // 'shift_header_PKID' => 'required',
                // 'UserID' => 'required|string|unique:t_employees,User_id',
                'phone_number' => 'required|unique:t_employees,phone_number',
            ], [
                'role_id.required' => 'Please select role field.',
                // 'shift_header_PKID.required' => 'Please select shift field.',
                // 'position_id.required' => 'Please select position field.',
                // 'UserID.required' => 'The User ID field is required',
                // 'UserID.unique' => 'This User ID is already exited, Please input again!',
            ]);

            $Format = DB::table('t_parameter')
                ->select('ValueOne')->where("ParameterCode", "StaffCodeFormat")->get()->first();
            $Keyword = "";
            $NumberFormat = 0;
            if ($Format != null) {
                $splitFormate = explode('+', $Format->ValueOne);
                $Keyword = count($splitFormate) > 0 ? $splitFormate[0] : "";
                $NumberFormat = count($splitFormate) > 1 ? $splitFormate[1] : 1;
            }

            $employee = new Employee();
            $employee->first_name = $request->firstName;
            $employee->last_name = $request->lastName;
            $employee->OtherName = $request->otherName;
            $employee->gender = $request->gender;
            $employee->email = $request->email;
            // $employee->postion_id = $request->position_id;
            $employee->start_date = $request->start_date;
            // $employee->ShiftHeaderPKID = $request->shift_header_PKID;
            // modify LINA 26/07/2023 #cms-80
            $employee->NationalID = $request->nationalID;
            $employee->BirthDate = $request->birthDate;
            $employee->BirthPlace = $request->birthPlace;
            $employee->HouseNo = $request->houseNo;
            $employee->StreetNo = $request->streetNo;
            $employee->VillageCode = $request->villageCode;
            $employee->Facebook = $request->facebook;
            $employee->Telegram = $request->telegram;
            $employee->OtherContact = $request->otherContact;
            // $employee->User_id = $request->UserID;

            $phoneEmpty = array(['phone' => null]);
            foreach ($request->phone_number as $phoneNum) {
                if ($phoneNum['phone'] != null) {
                    $employee->phone_number = $request->phone_number;
                } else {
                    $employee->phone_number = $phoneEmpty;
                }
            }

            $slug = Str::slug($request->name, '-');
            if ($request->hasfile('image')) {
                $this->validate($request, [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                $name = $slug . '-' . time() . '-' . $request->image->getClientOriginalName();
                \Image::make($request->image)->save(public_path('employees/') . $name);
                $employee->pic = $name;
            }
            $employee->profile_color = substr(uniqid(), -6);
            $employee->save();

            //insert staffcode
            $employee->StaffCode = $Format != null ? $Keyword . str_pad($employee->id, strlen($NumberFormat), $NumberFormat, STR_PAD_LEFT) : $employee->id;
            $employee->save();

            // modify by LINA 18-08-2023 #cms-85
            $user = new User();
            $user->role_id = $request->role_id;
            $user->employee_id = $employee->id;
            // $user->UserID = $request->UserID;
            // $user->UserID = $request->$employee->UserID;
            $user->UserID = substr($employee->first_name, 0, 1) . $employee->last_name;

            $defaultPassSysParam = DB::table('t_parameter')
                ->select('ValueOne', 'ValueTwo')
                ->where('ParameterCode', 'DefaultPass')
                ->get();
            $defaultPass = "khawin123";
            if (count($defaultPassSysParam) > 0) {
                $valueOne = $defaultPassSysParam[0]->ValueOne;
                $valueTwo = $defaultPassSysParam[0]->ValueTwo;
                if ($valueOne != "") {
                    $defaultPass = $valueOne;
                } else {
                    if ($valueTwo != "") {
                        $defaultPass = $valueTwo;
                    }
                }
            }
            $user->password = Hash::make($defaultPass);
            $user->save();

            $this->InsertSysLog('StaffInformation', $userPKID, 'Create', '', 'PK_ID: ' . $employee->id . ' Employee: ' . $request->firstName . " " . $request->lastName, 'Success', '');
            return response()->json(['message' => 'Employee data save successfully'], 200);
        } 
        catch(Exception $e) {
            $this->InsertSysLog('StaffInformation', $userPKID, 'Create', '', ' Employee: ' . $request->firstName . " " . $request->lastName, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'firstName' => 'required|string|min:2|max:100',
                'lastName' => 'required|string|min:2|max:100',
                'email' => 'required|email|max:255|regex:/(.*)\.com/i|unique:t_employees,email,' . $id,
                'gender' => 'required',
                'start_date' => 'required',
                // 'shift_header_PKID' => 'required',
                // 'position_id' => 'required',
                
                // 'UserID' => 'required|string|unique:t_employees,User_id,' . $id,
            ], [
                // 'shift_header_PKID.required' => 'Please select shift field.',
                // 'position_id.required' => 'The position field is required.',
                // 'UserID.unique' => 'This User Id or Phone Number is already existed.',
            ]);
            $employee = Employee::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldEmployeeData = 'PK_ID: ' . $employee->id . ' Employee: ' . $request->firstName . " " . $request->lastName;
            $employee->StaffCode = $request->ParameterPKID;
            $employee->first_name = $request->firstName;
            $employee->last_name = $request->lastName;
            $employee->OtherName = $request->otherName;
            $employee->gender = $request->gender;
            $employee->email = $request->email;
            // $employee->postion_id = $request->position_id;
            $employee->start_date = $request->start_date;
            // $employee->ShiftHeaderPKID = $request->shift_header_PKID;
            // modify LINA 26/07/2023 #cms-80
            $employee->NationalID = $request->nationalID;
            $employee->BirthDate = $request->birthDate;
            $employee->BirthPlace = $request->birthPlace;
            $employee->HouseNo = $request->houseNo;
            $employee->StreetNo = $request->streetNo;
            $employee->VillageCode = $request->villageCode;
            $employee->Facebook = $request->facebook;
            $employee->Telegram = $request->telegram;
            $employee->OtherContact = $request->otherContact;
            // $employee->User_id = $request->UserID;

            $phoneEmpty = array(['phone' => null]);
            foreach ($request->phone_number as $phoneNum) {
                if ($phoneNum['phone'] != null) {
                    $employee->phone_number = $request->phone_number;
                } else {
                    $employee->phone_number = $phoneEmpty;
                }
            }

            $slug = Str::slug($request->name, '-');
            if ($request->hasfile('image')) {
                $this->validate($request, [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                $url = public_path('employees/' . $employee->pic);
                if (file_exists($url)) {
                    unlink(public_path('employees/' . $employee->pic));
                }
                $name = $slug . '-' . time() . '-' . $request->image->getClientOriginalName();
                \Image::make($request->image)->save(public_path('employees/') . $name);
                $employee->pic = $name;
            }

            $employee->is_inactive = $request->is_inactived;
            if ($request->is_inactived) {
                $this->validate($request, [
                    'leave_date' => 'required',
                ]);
                $employee->leave_date = $request->leave_date;
            }
            $employee->save();

            $this->InsertSysLog('StaffInformation', $userPKID, 'Update', $oldEmployeeData, 'PK_ID: ' . $employee->id . ' Updated: ' . $request->firstName . " " . $request->lastName, 'Success', '');
            return response()->json(['message' => 'Employee update successfully.'], 200);
        } 
        catch (Exception $e) {
            $this->InsertSysLog('StaffInformation',$userPKID,'Update','PK_ID: '.$employee->id.' Employee: '.$request->firstName . " " . $request->lastName, 'PK_ID: ' . $employee->id . ' Error Employee: ' . $request->firstName . " " . $request->lastName,'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete($id)
    {
        $userPKID = Auth::user()->id;
        $employee = Employee::findOrFail($id);
        //Modified by Huychoung 22/08/2023 #87 Not allowed to delete employee if it being used
        $attendanceList = DB::table('t_staff_apply_leave_header')
            ->where('StaffPKID', $employee->id)
            ->get();
        $stringMessage = '';

        if (count($attendanceList) > 0) {
            $stringMessage .= 'Attendance List;';
        }
        $staffPosition = DB::table('t_staff_position')
            ->where('Employees_id', $employee->id)
            ->get();

        if (count($staffPosition) > 0) {
            $stringMessage .= 'Staff Position;';
        }
        $user = DB::table('t_users')
            ->where('employee_id', $employee->id)
            ->get();

        if (count($user) > 0) {
            $stringMessage .= 'User';
        }

        if ($stringMessage != '') {
            return response()->json(['errorMessage' => $stringMessage], 200);
        }

        $url = public_path('t_employees/' . $employee->pic);
        if (file_exists($url)) {
            unlink(public_path('employees/' . $employee->pic));
        }
        $employee->delete();
        
        $this->InsertSysLog('StaffInformation', $userPKID, 'Delete', 'PK_ID: ' . $employee->id . ' Employee: ' . $employee->first_name . " " . $employee->last_name, '', 'Success', '');
        return response()->json(['message' => 'Employee data delete successfully.'], 200);
    }
}
