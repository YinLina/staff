<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\EmployeeController;

class employeeData extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($employee){
                $concatStr = $employee->last_name != "" ? " " : "";
                $fullName = $employee->first_name . $concatStr . $employee->last_name;

                // midify Lina 09/08/2023 #cms-80 
                $communeCode = '';
                $districtCode = '';
                $provinceId = '';
                $villageListByCommuneCode = array();
                $communeListByDistrictCode = array();
                $districtListByProvinceCode = array();
                if($employee->VillageCode != null && $employee->VillageCode != "") {
                    $dbCommuneCode = DB::table('t_address_village')
                        ->select('CommuneCode')
                        ->where('VillageCode', $employee->VillageCode)
                        ->first();
                    if($dbCommuneCode != null) {
                        $communeCode = $dbCommuneCode->CommuneCode;
                        $villageListByCommuneCode = DB::table('t_address_village')
                            ->select('VillageCode', 'VillageName')
                            ->where('CommuneCode', $communeCode)
                            ->get();
                        $dbDistrictCode = DB::table('t_address_commune')
                            ->select('DistrictCode')
                            ->where('CommuneCode', $communeCode)
                            ->first();
                        if($dbDistrictCode != null) {
                            $districtCode = $dbDistrictCode->DistrictCode;
                            $communeListByDistrictCode = DB::table('t_address_commune')
                                ->select('CommuneCode', 'CommuneName')
                                ->where('DistrictCode', $districtCode)
                                ->get();
                            $dbProvinceCode = DB::table('t_address_district')
                                ->select('ProvinceCode')
                                ->where('DistrictCode', $districtCode)
                                ->first();
                            if($dbProvinceCode != null) {
                                $provinceCode = $dbProvinceCode->ProvinceCode;
                                $districtListByProvinceCode = DB::table('t_address_district')
                                    ->select('DistrictCode', 'DistrictName')
                                    ->where('ProvinceCode', $provinceCode)
                                    ->get();
                                $dbProvinceId = DB::table('t_address_province')
                                    ->select('PKID')
                                    ->where('ProvinceCode', $provinceCode)
                                    ->first();
                                if($dbProvinceId != null) {
                                    $provinceId = $dbProvinceId->PKID;
                                }
                            }
                        }
                    }
                }

                // get employee children
                $employeeChildrens = EmployeeController::readChildrenList($employee->id);
                $childrenList = array();
                for($i = 0; $i < count($employeeChildrens); $i++) {
                    $employeeChildrenModel = new employeeChildrenModel();
                    $employeeChildrenModel->PKID = $employeeChildrens[$i]->PKID;
                    $employeeChildrenModel->Employee_PKID = $employeeChildrens[$i]->Employee_PKID;
                    $employeeChildrenModel->FullName = $employeeChildrens[$i]->FullName;
                    $employeeChildrenModel->Gender = $employeeChildrens[$i]->Gender;
                    $employeeChildrenModel->DateOfBirth = $employeeChildrens[$i]->DateOfBirth;
                    $employeeChildrenModel->Remark = $employeeChildrens[$i]->Remark;
                    array_push($childrenList, $employeeChildrenModel);
                }
                // get employee education
                $employeeEducation = EmployeeController::readEducationList($employee->id);
                $educationList = array();
                for($i = 0; $i < count($employeeEducation); $i++) {
                    $employeeEducationModel = new EmployeeEducationModel();
                    $employeeEducationModel->PKID = $employeeEducation[$i]->PKID;
                    $employeeEducationModel->Employee_PKID = $employeeEducation[$i]->Employee_PKID;
                    $employeeEducationModel->SchoolName = $employeeEducation[$i]->SchoolName;
                    $employeeEducationModel->Address = $employeeEducation[$i]->Address;
                    $employeeEducationModel->StartDate = $employeeEducation[$i]->StartDate;
                    $employeeEducationModel->EndDate = $employeeEducation[$i]->EndDate;
                    $employeeEducationModel->Skill = $employeeEducation[$i]->Skill;
                    $employeeEducationModel->Detail = $employeeEducation[$i]->Detail;
                    array_push($educationList, $employeeEducationModel);
                }
                // get employee other skill
                $employeeOtherSkill = EmployeeController::readOtherSkillList($employee->id);
                $otherSkillList = array();
                for($i = 0; $i < count($employeeOtherSkill); $i++) {
                    $employeeOtherSkillModel = new employeeOtherSkillModel();
                    $employeeOtherSkillModel->PKID = $employeeOtherSkill[$i]->PKID;
                    $employeeOtherSkillModel->Employee_PKID = $employeeOtherSkill[$i]->Employee_PKID;
                    $employeeOtherSkillModel->TrainingPlace = $employeeOtherSkill[$i]->TrainingPlace;
                    $employeeOtherSkillModel->AddressOtherSkill = $employeeOtherSkill[$i]->Address;
                    $employeeOtherSkillModel->StartDateOtherSkill = $employeeOtherSkill[$i]->StartDate;
                    $employeeOtherSkillModel->EndDateOtherSkill = $employeeOtherSkill[$i]->EndDate;
                    $employeeOtherSkillModel->Skill = $employeeOtherSkill[$i]->Skill;
                    $employeeOtherSkillModel->Detail = $employeeOtherSkill[$i]->Detail;
                    array_push($otherSkillList, $employeeOtherSkillModel);
                }
                // get employee experience
                $employeeExperience = EmployeeController::readExperienceList($employee->id);
                $experienceList = array();
                for($i = 0; $i < count($employeeExperience); $i++) {
                    $employeeExperienceModel = new employeeExperienceModel();
                    $employeeExperienceModel->PKID = $employeeExperience[$i]->PKID;
                    $employeeExperienceModel->Employee_PKID = $employeeExperience[$i]->Employee_PKID;
                    $employeeExperienceModel->Company = $employeeExperience[$i]->Company;
                    $employeeExperienceModel->AddressExperience = $employeeExperience[$i]->Address;
                    $employeeExperienceModel->StartDateExperience = $employeeExperience[$i]->StartDate;
                    $employeeExperienceModel->EndDateExperience = $employeeExperience[$i]->EndDate;
                    $employeeExperienceModel->Position = $employeeExperience[$i]->Position;
                    $employeeExperienceModel->Detail = $employeeExperience[$i]->Detail;
                    array_push($experienceList, $employeeExperienceModel);
                }
                // get employee language
                $employeeLanguage = EmployeeController::readLanguageList($employee->id);
                $languageList = array();
                for($i = 0; $i < count($employeeLanguage); $i++) {
                    $employeeLanguageModel = new employeeLanguageModel();
                    $employeeLanguageModel->PKID = $employeeLanguage[$i]->PKID;
                    $employeeLanguageModel->Employee_PKID = $employeeLanguage[$i]->Employee_PKID;
                    $employeeLanguageModel->Language = $employeeLanguage[$i]->Language;
                    $employeeLanguageModel->Level = $employeeLanguage[$i]->Level;
                    $employeeLanguageModel->Detail = $employeeLanguage[$i]->Detail;
                    array_push($languageList, $employeeLanguageModel);
                }
                // get employee reference
                $employeeReference = EmployeeController::readReferenceList($employee->id);
                $referenceList = array();
                for($i = 0; $i < count($employeeReference); $i++) {
                    $employeeReferenceModel = new employeeReferenceModel();
                    $employeeReferenceModel->PKID = $employeeReference[$i]->PKID;
                    $employeeReferenceModel->Employee_PKID = $employeeReference[$i]->Employee_PKID;
                    $employeeReferenceModel->FullName = $employeeReference[$i]->FullName;
                    $employeeReferenceModel->Position = $employeeReference[$i]->Position;
                    $employeeReferenceModel->Company = $employeeReference[$i]->Company;
                    $employeeReferenceModel->Email = $employeeReference[$i]->Email;
                    $employeeReferenceModel->PhoneNumber = $employeeReference[$i]->PhoneNumber;
                    array_push($referenceList, $employeeReferenceModel);
                }
                $role = DB::table('t_users')
                ->join('t_roles','t_roles.id','t_users.role_id')
                ->where('t_users.employee_id',$employee->id)
                ->select('t_roles.id','t_roles.role')
                ->first();
                return[
                    'id' => $employee->id,
                    'fullName' => $fullName,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'OtherName' => $employee->OtherName,
                    'email' => $employee->email,
                    'gender' => $employee->gender,
                    // 'position' => $employee->position,
                    'start_date' => $employee->start_date,
                    'phone_number' => $employee->phone_number,
                    'role' => $role,
                    // 'ShiftName' => $employee->shift,
                    'image' => $employee->pic,
                    'profile_color' => $employee->profile_color,
                    'is_inactived' => $employee->is_inactive,
                    'leave_date' => $employee->leave_date,
                    'StaffCodeID' =>$employee->StaffCode,
                    'NationalID' =>$employee->NationalID,
                    'BirthDate' =>$employee->BirthDate,
                    'BirthPlace' =>$employee->BirthPlace,
                    'HouseNo' =>$employee->HouseNo,
                    'StreetNo' =>$employee->StreetNo,
                    'VillageCode' =>$employee->VillageCode,
                    'CommuneCode' => $communeCode,
                    'DistrictCode' => $districtCode,
                    'ProvinceId' => $provinceId,
                    'villageListByCommuneCode' => $villageListByCommuneCode,
                    'communeListByDistrictCode' => $communeListByDistrictCode,
                    'districtListByProvinceCode' => $districtListByProvinceCode,
                    'Facebook' =>$employee->Facebook,
                    'Telegram' =>$employee->Telegram,
                    'OtherContact' =>$employee->OtherContact,
                    // 'User_id' =>$employee->User_id,
                    'employeeChildren' => $childrenList,
                    'employeeEducation' => $educationList,
                    'employeeOtherSkill' => $otherSkillList,
                    'employeeExperience' => $experienceList,
                    'employeeLanguage' => $languageList,
                    'employeeReference' => $referenceList,
                ];
            })
        ];
    }
}

class employeeChildrenModel {
    public $PKID;
    public $Employee_PKID;
    public $FullName;
    public $Gender;
    public $DateOfBirth;
    public $Remark; 
}
class employeeEducationModel {
    public $PKID;
    public $Employee_PKID;
    public $SchoolName;
    public $Address;
    public $StartDate;
    public $EndDate;
    public $Skill; 
    public $Detail; 
}
class employeeOtherSkillModel {
    public $PKID;
    public $Employee_PKID;
    public $TrainingPlace;
    public $AddressOtherSkill;
    public $StartDateOtherSkill;
    public $EndDateOtherSkill;
    public $Skill; 
    public $Detail; 
}
class employeeLanguageModel {
    public $PKID;
    public $Employee_PKID;
    public $Language;
    public $Level;
    public $Detail; 
}
class employeeExperienceModel {
    public $PKID;
    public $Employee_PKID;
    public $Company;
    public $AddressExperience;
    public $StartDateExperience;
    public $EndDateExperience;
    public $Position; 
    public $Detail; 
}
class employeeReferenceModel {
    public $PKID;
    public $Employee_PKID;
    public $FullName;
    public $Position; 
    public $Company; 
    public $Email; 
    public $PhoneNumber;
}
