<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffShiftModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_staff_shift';

    public function shift(){
        return $this->belongsTo(ShiftModel::class, 'ShiftHeader_PKID');
    }

    public function shiftDetail(){
        return $this->hasMany(ShiftDetail::class, 'ShiftHeader_PKID');
    }
}

