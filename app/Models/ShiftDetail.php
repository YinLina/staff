<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_shift_detail';

    public function shift() {
        return $this->belongsTo(ShiftModel::class, 'ShiftHeaderPKID');
    }

    public function staffShift() {
        return $this->hasMany(StaffShiftModel::class, 'ShiftHeader_PKID');
    }

    public function applyleave_detail(){
        return $this->HasMany(ApplyLeaveDetail::class, 'ShiftDetailPKID');
    }
}
