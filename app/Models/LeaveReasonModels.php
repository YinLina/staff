<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveReasonModels extends Model
{
    use HasFactory;
    protected $primaryKey = 'PK_ID';
    protected $table = 't_leave_reason';

    public function applyleave() {
        return $this->hasMany(ApplyLeave::class, 'LeaveReasonPKID');
    }

    public function applyLeaveHeader(){
        return $this->hasMany(LeaveRequestModel::class, 'LeaveReasonPKID');
    }
}
