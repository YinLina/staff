<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOtherSkillModel extends Model
{
    use HasFactory;
    protected $table = 't_employee_other_skills';
    protected $primaryKey = 'PKID';
}
