<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducationModel extends Model
{
    use HasFactory;
    protected $table = 't_employee_education';
    protected $primariKey = 'PKID';
}
