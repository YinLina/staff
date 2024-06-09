<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationProcess extends Model
{
    use HasFactory;
    protected $table = 't_validation_process';
    protected $primaryKey = 'PKID';
    public function applyleave_header(){
        return $this->belongsTo(ApplyLeave::class, 'StaffApplyLeaveHeaderPKID');
    }
    public function group(){
        return $this->belongsTo(Group::class, 'GroupPKID');
    }
}
