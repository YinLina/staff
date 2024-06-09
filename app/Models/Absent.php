<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    use HasFactory;
    protected $table = 't_absents';
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
