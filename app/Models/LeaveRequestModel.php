<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_staff_apply_leave_header';

    public function leaveReason() {
        return $this->belongsTo(LeaveReasonModels::class, 'LeaveReasonPKID');
    }

    public function leaveType() {
        return $this->belongsTo(LeaveType::class, 'LeaveTypePKID');
    }

    public function staff() {
        return $this->belongsTo(Employee::class, 'StaffPKID');
    }

    public function applyleave_detail(){
        return $this->hasMany(ApplyLeaveDetail::class, 'StaffApplyLeaveHeaderPKID');
    }
}
