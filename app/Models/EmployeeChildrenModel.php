<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeChildrenModel extends Model
{
    use HasFactory;
    protected $table = 't_employee_children';
    protected $primaryKey = 'PKID';
}
