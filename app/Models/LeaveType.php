<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;
    protected $primaryKey = 'PK_ID';
    protected $table = 't_leave_type';

    public function applyleave() {
        return $this->hasMany(ApplyLeave::class, 'LeaveTypePKID');
    }

    public function applyLeaveHeader(){
        return $this->hasMany(LeaveRequestModel::class, 'LeaveTypePKID');
    }
}
