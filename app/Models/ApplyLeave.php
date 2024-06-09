<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyLeave extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_staff_apply_leave_header';

    public function staff() {
        return $this->belongsTo(Employee::class, 'StaffPKID');
    }

    public function leavereason() {
        return $this->belongsTo(LeaveReasonModels::class, 'LeaveReasonPKID');
    }

    public function leavetype() {
        return $this->belongsTo(LeaveType::class, 'LeaveTypePKID');
    }
    
    public function menu(){
        return $this->belongsTo(MenuSystemModel::class,'MenuPKID');
    }

    public function department() {
        return $this->belongsTo(DepartmentModel::class, 'DepartmentPKID');
    }

    //New
    public function applyleave_detail(){
        return $this->hasMany(ApplyLeaveDetail::class, 'StaffApplyLeaveHeaderPKID');
    }

    //New
    public function validation(){
        return $this->HasMany(ValidationProcess::class, 'StaffApplyLeaveHeaderPKID');
    }

    public function ValidationStatus() {
        return $this->belongsTo(ValidationModel::class, 'Status');
    }
}
