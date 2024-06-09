<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyLeaveDetail extends Model
{
    //New
    use HasFactory;
    protected $table = 't_staff_apply_leave_detail';
    protected $primaryKey = 'PKID';

    public function applyleave_header(){
        return $this->belongsTo(ApplyLeave::class, 'StaffApplyLeaveHeaderPKID');
    }

    public function shift_detail(){
        return $this->BelongsTo(ShiftDetail::class, 'ShiftDetailPKID');
    }

    public function applyleave_detail(){
        return $this->belongsTo(LeaveRequestModel::class, 'StaffApplyLeaveHeaderPKID');
    }
}
