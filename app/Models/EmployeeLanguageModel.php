<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLanguageModel extends Model
{
    use HasFactory;
    protected $table = 't_employee_languages';
    protected $primaryKey = 'PKID';
}
