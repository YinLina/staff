<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 't_employees';
    protected $casts = [
        'phone_number' => 'array'
    ];

    public function absent(){
        return $this->hasMany(Absent::class, 'employee_id');
    }

    public function position(){
        return $this->belongsTo(Position::class, 'id');
    }

    public function shift(){
        return $this->belongsTo(ShiftModel::class, 'ShiftHeaderPKID');
    }

    public function applyleave(){
        return $this->hasMany(ApplyLeave::class, 'StaffPKID');
    }

    public function user(){
        return $this->hasMany(User::class, 'employee_id');
    }

    public function applyLeaveHeader(){
        return $this->hasMany(LeaveRequestModel::class, 'StaffPKID');
    }
    public function parameter(){
        return $this->belongsTo(ParameterModel::class, 'StaffCode');
    }
}
