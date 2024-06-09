<?php

use App\Http\Controllers\Api\AbsentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\Mobile\AbsentController as MobileAbsentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaveTypeController;
use App\Http\Controllers\Api\ApplyLeaveController;
use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\ParameterController;
use App\Http\Controllers\Api\LeaveReasonController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\MenuSystemController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\GroupController;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\LeaveEntryController;
use App\Http\Controllers\Api\StaffPositionController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\SystemLogController;
use App\Http\Controllers\Api\StaffResignationController;
use App\Http\Controllers\Api\ResignationReasonController;
use App\Http\Controllers\Api\StaffShiftController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    // mdbdasl
});


// ------Authencation------
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgot']);
Route::post('/password/reset', [ForgotPasswordController::class, 'reset']);
// =========== Theara #CMS-43 Remember Password=================
Route::get('get-user-login/{id}', [AuthController::class, 'readUserLogin']);
Route::group(['middleware' => ['auth:sanctum']], function () {

    // ===============MOBILE================
    Route::get('mobile/read-absent', [MobileAbsentController::class, 'read']);
    // ===============/MOBILE/==============

    // ------------Menu---------------
    Route::get('menu-data', [MenuController::class, 'read']);
    Route::get('role-menu', [MenuController::class, 'roleMenu']);
    // Route::post('create-role', [MenuController::class, 'create']);

    // ------------Role----------------
    Route::get('role-data', [RoleController::class, 'read']);
    Route::get('role-user', [RoleController::class, 'role']);
    Route::post('create-role', [RoleController::class, 'store']);
    Route::post('update-role/{id}', [RoleController::class, 'update']);
    Route::delete('delete-role/{id}', [RoleController::class, 'delete']);

    // --------Dashboard-------
    Route::get('dashboard-data', [DashboardController::class, 'read']);

    // ----------user----------
    Route::get('read-user', [UserController::class, 'read']);
    Route::post('create-user', [UserController::class, 'create']);
    Route::post('update-user/{id}', [UserController::class, 'update']);
    Route::delete('delete-user/{id}', [UserController::class, 'delete']);

    // ---------Position-------
    Route::get('read-position', [PositionController::class, 'read']);
    Route::post('create-position', [PositionController::class, 'create']);
    Route::post('update-position/{id}', [PositionController::class, 'update']);
    Route::delete('delete-position/{id}', [PositionController::class, 'delete']);

    // --------Employee--------
    Route::get('active-employee', [EmployeeController::class, 'active']);
    Route::get('inactive-employee', [EmployeeController::class, 'inactive']);
    Route::post('create-employee', [EmployeeController::class, 'create']);
    Route::post('update-employee/{id}', [EmployeeController::class, 'update']);
    Route::delete('delete-employee/{id}', [EmployeeController::class, 'delete']);
    Route::get('read-shift-detail', [EmployeeController::class, 'read']);
    Route::get('read-province', [EmployeeController::class, 'readProvinceForm']);
    Route::get('get-district-by-code/{id}', [EmployeeController::class, 'readDistrictByProvinceCode']);
    Route::get('get-commune-by-code/{id}', [EmployeeController::class, 'readCommuneByDistrictCode']);
    Route::get('get-village-by-code/{id}', [EmployeeController::class, 'readCommuneByVillageCode']);

    Route::post('create-or-update-emp-child', [EmployeeController::class, 'createOrUpdateEmpChild']);
    Route::post('create-or-update-emp-education', [EmployeeController::class, 'createOrUpdateEmpEducation']);
    Route::post('create-or-update-emp-other-skill', [EmployeeController::class, 'createOrUpdateEmpOtherSkill']);
    Route::post('create-or-update-emp-experience', [EmployeeController::class, 'createOrUpdateEmpExperience']);
    Route::post('create-or-update-emp-language', [EmployeeController::class, 'createOrUpdateEmpLanguage']);
    Route::post('create-or-update-emp-reference', [EmployeeController::class, 'createOrUpdateEmpReference']);
    Route::get('read-children-list/{id}', [EmployeeController::class, 'readChildrenList']);
    Route::get('read-education-list/{id}', [EmployeeController::class, 'readEducationList']);
    Route::get('read-other-skill-list/{id}', [EmployeeController::class, 'readOtherSkillList']);
    Route::get('read-experience-list/{id}', [EmployeeController::class, 'readExperienceList']);
    Route::get('read-language-list/{id}', [EmployeeController::class, 'readLanguageList']);
    Route::get('read-reference-list/{id}', [EmployeeController::class, 'readReferenceList']);

    // ---------Absent----------
    Route::get('read-absent', [AbsentController::class, 'read']);
    Route::post('create-absent', [AbsentController::class, 'create']);
    Route::post('update-absent/{id}', [AbsentController::class, 'update']);
    Route::delete('delete-absent/{id}', [AbsentController::class, 'delete']);

    // ---------Report----------
    Route::post('read-report', [ReportController::class, 'report']);
    Route::post('update-profile/{id}', [AuthController::class, 'update']);
    Route::post('update-password/{id}', [AuthController::class, 'password']);
    Route::post('logout', [AuthController::class, 'logout']);

    // -------LeaveType----------
    Route::get('read-leave-type', [LeaveTypeController::class, 'read']);
    Route::post('create-leave-type', [LeaveTypeController::class, 'create']);
    Route::post('update-leave-type/{id}', [LeaveTypeController::class, 'update']);
    Route::delete('delete-leave-type/{id}', [LeaveTypeController::class, 'delete']);

    // --------ApplyLeave-----------
    Route::get('read-apply-leave/{lang}', [ApplyLeaveController::class, 'read']);
    Route::post('create-apply-leave-save-or-submit', [ApplyLeaveController::class, 'create']);
    Route::post('update-apply-leave-save-or-submit/{id}', [ApplyLeaveController::class, 'update']);
    Route::delete('delete-apply-leave/{id}', [ApplyLeaveController::class, 'delete']);

    // -------Asset Category--------
    Route::get('read-asset-category', [AssetCategoryController::class, 'read']);
    Route::post('create-asset-category', [AssetCategoryController::class, 'create']);
    Route::post('update-asset-category/{id}', [AssetCategoryController::class, 'update']);
    Route::delete('delete-asset-category/{id}', [AssetCategoryController::class, 'delete']);

    // -------- Department ---------
    Route::get('read-department', [DepartmentController::class, 'read']);
    Route::post('create-department', [DepartmentController::class, 'create']);
    Route::post('update-department/{id}', [DepartmentController::class, 'update']);
    Route::delete('delete-department/{id}', [DepartmentController::class, 'delete']);

   // -----------Parameter---------
    Route::get('read-parameter', [ParameterController::class, 'read']);
    Route::post('create-parameter',[ParameterController::class,'create']);
    Route::post('update-parameter/{id}', [ParameterController::class, 'update']);
    Route::delete('delete-parameter/{id}', [ParameterController::class, 'delete']);
    Route::get('read-validationtype', [ParameterController::class, 'GetValidationType']);
    Route::get('get-staff-code', [ParameterController::class, 'ReadParameter']);

    // -------LeaveReason----------
    Route::get('read-leave-reason', [LeaveReasonController::class, 'read']);
    Route::post('create-leave-reason', [LeaveReasonController::class, 'create']);
    Route::post('update-leave-reason/{id}', [LeaveReasonController::class, 'update']);
    Route::delete('delete-leave-reason/{id}', [LeaveReasonController::class, 'delete']);

    //  --------MenuSystem --------
    Route::get('read-menuSystem',[MenuSystemController::class, 'read']);
    Route::post('create-menuSystem', [MenuSystemController::class, 'create']);
    Route::post('update-menuSystem/{id}', [MenuSystemController::class, 'update']);
    Route::delete('delete-menuSystem/{id}', [MenuSystemController::class, 'delete']);

    // -----------Branh------------
    Route::get('read-branch', [BranchController::class, 'read']);
    Route::post('create-branch', [BranchController::class, 'create']);
    Route::post('update-branch/{id}', [BranchController::class, 'update']);
    Route::delete('delete-branch/{id}', [BranchController::class, 'delete']);

    // ------------Work Flow-----------------
    Route::get('read-workflow', [WorkFlowController::class, 'read']);
    Route::post('create-workflow', [WorkFlowController::class, 'create']);
    Route::delete('delete-workflow/{id}', [WorkFlowController::class,'delete']);
    Route::post('update-workflow/{id}', [WorkFlowController::class, 'update']);
    Route::get('read-position-update/{id}', [WorkFlowController::class, 'UpdatePosition']);
    Route::get('read-position-type/{id}', [WorkFlowController::class, 'readPosition']);

    // ---------------Shift------------------ 
    Route::get('read-shift-employee/{id}', [ShiftController::class, 'read']);
    Route::get('read-shift-header', [ShiftController::class, 'readShift']);
    Route::get('read-shift-detail', [ShiftController::class, 'readShiftDetail']);
    Route::post('create-shift', [ShiftController::class, 'create']);
    Route::post('update-shift/{id}', [ShiftController::class, 'update']);
    Route::delete('delete-shift/{id}', [ShiftController::class,'delete']);
    
    //-----------------Group-----------------
    Route::get('read-group', [GroupController::class, 'read']);
    Route::post('create-group', [GroupController::class, 'create']);
    Route::post('update-group/{id}', [GroupController::class, 'update']);
    Route::delete('delete-group/{id}', [GroupController::class, 'delete']);

    //-----------------Leave Request---------
    Route::get('read-leave-request', [LeaveRequestController::class, 'read']);
    Route::post('update-leave-request-status-approve/{id}', [LeaveRequestController::class,'update']);
    
    // --------LeaveEntry--------------
    Route::get('read-employees-by-department-id/{id}', [LeaveEntryController::class, 'readStaff']);
    Route::post('create-staff-apply-leave-header', [LeaveEntryController::class, 'createStaffApplyLeave']);
    Route::post('read-leave-entry', [LeaveEntryController::class, 'read']);
    Route::post('create-leave-entry', [LeaveEntryController::class, 'create']);
    Route::post('update-leave-entry/{id}', [LeaveEntryController::class, 'update']);
    Route::delete('delete-leave-entry/{id}', [LeaveEntryController::class, 'delete']);
    Route::get('read-staff', [LeaveEntryController::class, 'readStaff']);

    // --------Holiday----------------
    Route::get('read-holiday', [HolidayController::class, 'read']);
    Route::get('read-holidayType-for-combobox', [HolidayController::class, 'readHolidayTypeForCombobox']);
    Route::post('create-holiday', [HolidayController::class, 'create']);
    Route::post('update-holiday/{id}', [HolidayController::class, 'update']);
    Route::delete('delete-holiday/{id}', [HolidayController::class, 'delete']);

    // --------Staff Position---------
    Route::get('read-staff-position', [StaffPositionController::class, 'read']);
    Route::post('create-staff-position', [StaffPositionController::class, 'create']);
    Route::post('update-staff-position/{id}', [StaffPositionController::class, 'update']);
    Route::delete('delete-staff-position/{id}', [StaffPositionController::class, 'delete']);

    // --------Document--------------
    Route::get('read-document', [DocumentController::class, 'read']);
    Route::get('read-document-type', [DocumentController::class, 'readDocumentTypeForCombobox']);
    Route::post('create-document', [DocumentController::class, 'create']);
    Route::post('update-document/{id}', [DocumentController::class, 'update']);
    Route::post('delete-document_by_ids', [DocumentController::class, 'delete']);
    Route::get('download-document/{id}', [DocumentController::class, 'download']);

    // ---------System Log-----------
    Route::get('read-system-log', [SystemLogController::class, 'read']);

    // -----Staff Resignation--------
    Route::get('read-staff-resignation', [StaffResignationController::class, 'read']);
    Route::get('read-staff-resignation-reason',[StaffResignationController::class, 'readResignationReason']);
    Route::post('create-staff-resignation', [StaffResignationController::class, 'create']);
    Route::post('update-staff-resignation/{id}', [StaffResignationController::class, 'update']);
    Route::delete('delete-staff-resignation/{id}', [StaffResignationController::class, 'delete']);

    // -----Resignation Reason-------
    Route::get('read-resignation-reason', [ResignationReasonController::class, 'read']);
    Route::post('create-resignation-reason', [ResignationReasonController::class, 'create']);
    Route::post('update-resignation-reason/{id}', [ResignationReasonController::class, 'update']);
    Route::delete('delete-resignation-reason/{id}', [ResignationReasonController::class, 'delete']);

    // ---------Staff Shift----------
    Route::get('read-staff-shift', [StaffShiftController::class, 'read']);
    Route::get('read-shift', [StaffShiftController::class, 'readShift']);
    Route::post('create-staff-shift', [StaffShiftController::class, 'create']);
    Route::post('update-staff-shift/{id}', [StaffShiftController::class, 'update']);
    Route::post('delete_staff_shift_by_ids', [StaffShiftController::class, 'delete']);
    // Route::post('edit-or-update-staff-shift', [StaffShiftController::class, 'updateStaffShift']);
});



