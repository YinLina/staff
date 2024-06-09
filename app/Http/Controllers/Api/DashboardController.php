<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absent;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveRequestModel;
use Auth;
use App\Models\ValidationProcess;
use App\Models\ApplyLeave;
use Illuminate\Support\Facades\Schema;
use App\Models\GroupUser;
use Illuminate\Support\Arr;
class DashboardController extends Controller
{
    public function read()
    {
        $now = Carbon::now();
        $this-> CreateValidationStatusTemporary();
        $userId = Auth::user()->id;
        $leaveRequest = DB::table('t_staff_apply_leave_header')
        ->join('t_employees','t_employees.id','t_staff_apply_leave_header.StaffPKID')
        ->join('t_leave_reason','t_leave_reason.PK_ID','t_staff_apply_leave_header.LeaveReasonPKID')
        ->join('t_leave_type', 't_leave_type.PK_ID', 't_staff_apply_leave_header.LeaveTypePKID')
        ->join('temp_ValidationStatus','temp_ValidationStatus.StatusId','t_staff_apply_leave_header.Status')
        ->join('t_validation_process', 't_validation_process.StaffApplyLeaveHeaderPKID', 't_staff_apply_leave_header.PKID')
        ->join('t_group_user', 't_group_user.GroupPKID', 't_validation_process.GroupPKID')
        ->join('t_staff_apply_leave_detail', 't_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID','t_staff_apply_leave_header.PKID')
        ->select(
            't_staff_apply_leave_header.PKID', 
            't_employees.first_name','t_employees.last_name',
            't_staff_apply_leave_header.SubmittedDate',
            't_leave_reason.LeaveReason',
            'temp_ValidationStatus.Status',
            't_leave_type.LeaveType',
            't_staff_apply_leave_header.Remark',
            't_staff_apply_leave_header.ValidationType',
            'AttendanceDate'
        )
        ->where('t_group_user.UserPKID', $userId)
        ->where('t_validation_process.Status',1)
        ->get()->toArray();
        $leaverequestAll = count($leaveRequest);
        $this->CreateValidationTypeTemporary();
        $validationType = DB::table('temp_ValidationType')->select('ValidationTypeId','ValidationType')->get();
        $CountWh= array();
        foreach($validationType as $vt){
            $filteredArray = Arr::where($leaveRequest, function ($value, $key) use($vt) {
                return $value->ValidationType == $vt->ValidationTypeId;
            });
            $CountVT = new FilterValidation();
            $CountVT->total = count($filteredArray);
            $CountVT->validationType = $vt->ValidationType;
            array_push($CountWh,$CountVT);
        }
        
        //=================================
        $positionData = array();
        $positions = Position::withCount('employee')->get();
        foreach($positions as $position){
            if($position->employee_count > 0){
                $positionData[] = array(
                    'positions' => $position->title,
                    'count' => $position->employee_count
                );
            }
        }
        
        $userCount = User::where('id', '!=', auth('sanctum')->user()->id)->count();
        $role = Role::select('id', 'role')
            ->withCount('user')
            ->with(['user' => function ($q){
                $q->where('id', '!=', auth('sanctum')->user()->id);
            }])
            ->get();

        $roleUser = array();
        foreach($role as $item){
            $roleUser[] = [
                'role' => $item->role,
                'user' => count($item->user),
            ];
        }

        $employeesCount = Employee::count();
        // $year = Carbon::now()->year;
        // $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        // $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        // // ------ABSENTS-WEEKLY-----
        // $absentWeeklyData = array();
        // $weeklyAbsentsCount =  DB::table('t_absents')
        //     ->join('t_employees', 't_employees.id', '=' ,'t_absents.employee_id')
        //     ->where('date', '>=' ,$weekStartDate)
        //     ->where('date', '<=', $weekEndDate)
        //     ->where('is_inactive', false)
        //     ->count();

        // $absentWeeklys = Employee::whereHas('absent')->with('absent', function($q) use($weekStartDate, $weekEndDate){
        //     $q->where('date', '>=' ,$weekStartDate);
        //     $q->where('date', '<=', $weekEndDate);
        //     $q->orderBy('date', 'DESC')->get();
        // })->where('is_inactive', false)->get();

        // foreach($absentWeeklys as $absentWeekly){
        //     if(count($absentWeekly->absent) > 0){
        //         $absent_total = 0;
        //         foreach($absentWeekly->absent as $absent)
        //         {
        //             $absent_total = $absent_total + $absent->number;
        //         }
        //         $absentWeeklyData[] = array(
        //             'id' => $absentWeekly->id,
        //             'name' => $absentWeekly->name,
        //             'pic' => $absentWeekly->pic,
        //             'profile_color' => $absentWeekly->profile_color,
        //             'absent_count' => count($absentWeekly->absent),
        //             'absent_total' => $absent_total,
        //             'absents' => $absentWeekly->absent
        //         );
        //     }
        // }

        // // ------ABSENTS-MONTHLY-----
        // $absentMonthlyData = array();
        // $monthlyAbsentCount = DB::table('t_absents')
        //     ->join('t_employees', 't_employees.id', '=' ,'t_absents.employee_id')
        //     ->whereMonth('date', Carbon::now()->month)
        //     ->where('is_inactive', false)
        //     ->count();

        // $absentMonthlys = Employee::whereHas('absent')->with('absent', function($q){
        //     $q->whereMonth('date', Carbon::now()->month);
        //     $q->orderBy('date', 'DESC')->get();
        // })->where('is_inactive', false)->get();

        // foreach($absentMonthlys as $absentMonthly){
        //     if(count($absentMonthly->absent) > 0){
        //         $absent_total = 0;
        //         foreach($absentMonthly->absent as $absent)
        //         {
        //             $absent_total = $absent_total + $absent->number;
        //         }
        //         $absentMonthlyData[] = array(
        //             'id' => $absentMonthly->id,
        //             'name' => $absentMonthly->name,
        //             'pic' => $absentMonthly->pic,
        //             'profile_color' => $absentMonthly->profile_color,
        //             'absent_count' => count($absentMonthly->absent),
        //             'absent_total' => $absent_total,
        //             'absents' => $absentMonthly->absent
        //         );
        //     }
        // }
        // // return $absentMonthlyData;


        // // ------ABSENTS-YEARLY-----
        // $absentYearlyData = array();
        // $yearAbsentCount = DB::table('t_absents')
        //     ->join('t_employees', 't_employees.id', '=' ,'t_absents.employee_id')
        //     ->where('date', $year)
        //     ->where('is_inactive', false)
        //     ->count();

        // $absentYearlys = Employee::whereHas('absent')->with('absent', function($q) use ($year){
        //     $q->where('date', $year);
        // })->where('is_inactive', false)->get();

        // foreach($absentYearlys as $absentYearly){
        //     if(count($absentYearly->absent) > 0){
        //         $absent_total = 0;
        //         foreach($absentYearly->absent as $absent)
        //         {
        //             $absent_total = $absent_total + $absent->number;
        //         }
        //         $absentYearlyData[] = array(
        //             'id' => $absentYearly->id,
        //             'name' => $absentYearly->name,
        //             'pic' => $absentYearly->pic,
        //             'profile_color' => $absentYearly->profile_color,
        //             'absent_count' => count($absentYearly->absent),
        //             'absent_total' => $absent_total,
        //             'absents' => $absentYearly->absent
        //         );
        //     }
        // }

        // $absentsCount = DB::table('t_absents')
        //     ->join('t_employees', 't_employees.id', '=' ,'t_absents.employee_id')
        //     ->where('is_inactive', false)
        //     ->count();

        $TodayAbsent = DB::table('t_staff_apply_leave_header')
            ->join('t_staff_apply_leave_detail','t_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID','t_staff_apply_leave_header.PKID')
            ->join('t_employees','t_employees.id','t_staff_apply_leave_header.StaffPKID')
            ->join('t_shift_detail','t_shift_detail.PKID','t_staff_apply_leave_detail.ShiftDetailPKID')
            ->join('temp_ValidationType','temp_ValidationType.ValidationTypeId','t_staff_apply_leave_header.ValidationType')
            ->select('first_name','last_name','TimeIn','TimeOut','t_employees.id','ValidationTypeId','temp_ValidationType.ValidationType')
            ->where('Status',3)
            ->where('AttendanceDate',$now->today())
            ->orderBy('TimeIn')
            ->get()->toArray();

        $newTodayAbsent = DB::table('t_staff_apply_leave_header')
            ->join('t_employees','t_employees.id','t_staff_apply_leave_header.StaffPKID')
            ->join('t_staff_apply_leave_detail','t_staff_apply_leave_detail.StaffApplyLeaveHeaderPKID','t_staff_apply_leave_header.PKID')
            ->join('temp_ValidationType','temp_ValidationType.ValidationTypeId','t_staff_apply_leave_header.ValidationType')
            ->select('StaffPKID','first_name','last_name','temp_ValidationType.ValidationType','ValidationTypeId')->distinct()
            ->where('Status',3)
            ->where('AttendanceDate' , $now->today())
            ->get();
            Schema::drop('temp_ValidationStatus');
            Schema::drop('temp_ValidationType');
        $lastResult = array();
        if(count($newTodayAbsent) > 0){
            foreach($newTodayAbsent as $data){
                $filter = Arr::where($TodayAbsent, function($value, $key) use($data){
                    return $value->id == $data->StaffPKID && $value->ValidationTypeId == $data->ValidationTypeId;
                });
                $stringTime="";
                $validationType;
                if(count($filter) > 0){
                    foreach($filter as $d){
                        $TimeIn = $d->TimeIn;
                        $TimeOut = $d->TimeOut;
                        $TimeIn = Str::replacelast(':00','',$TimeIn);
                        $TimeOut = str::replacelast(':00','',$TimeOut);
                        $stringTime.= $TimeIn .' - '. $TimeOut. '  ';
                        $validationType = $d->ValidationType;
                    }

                };
                $todayAbsentModel = new TodayAbsentModel();
                $todayAbsentModel->name = $data->first_name.' '.$data->last_name;
                $todayAbsentModel->absentTime = $stringTime;
                $todayAbsentModel->validationType = $validationType;
                array_push($lastResult, $todayAbsentModel);
            }
        }
        return response()->json([
            'user' => [
                'user' => $userCount,
                'role' => $roleUser
            ],
            'employee' => [
                'allEmployee' =>  $employeesCount,
                'positions' => $positionData,
            ],
            'absent' => [
                // 'absentCount' => $absentsCount,
                // 'absentWeekCount' => $weeklyAbsentsCount,
                // 'absentMonthCount' => $monthlyAbsentCount,
                // 'absentYearCount' => $yearAbsentCount,
                // 'weeklyAbsents' => $absentWeeklyData,
                // 'monthlyAbsents' => $absentMonthlyData,
                // 'yearAbsents' => $absentYearlyData,
                // 'yearAbsentCount' => $yearAbsentCount,
                'TodayAbsent' => $lastResult
            ],
            'leaverequest' => [
                'leaverequestCount' => $leaverequestAll,
                'CountWorkFromHome' => $CountWh,
            ]
        ]);
    }
}

class TodayAbsentModel {
    public $name;
    public $absentTime;
    public $validationType;
}
class FilterValidation {
    public $total;
    public $validationType;
}
