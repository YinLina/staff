<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_shift_header';
  
    public function shiftDetail(){
        return $this->hasMany(ShiftDetail::class, 'ShiftHeaderPKID');
    }

    public function employees() {
        return $this->hasMany(Employee::class, 'ShiftHeaderPKID');
    }

    public function staffShift() {
        return $this->hasMany(StaffShiftModel::class, 'ShiftHeader_PKID');
    }
}