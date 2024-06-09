<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $table = 't_positions';

    public function employee(){
        return $this->hasMany(Employee::class, 'id');
    }

    public function staffPosition(){
        return $this->hasMany(StaffPositionModel::class, 'Positions_id');
    }

    public function department() {
        return $this->belongsTo(DepartmentModel::class, 'Department_PKID');
    }
}
