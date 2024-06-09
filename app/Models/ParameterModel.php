<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table ='t_parameter';

    public function employee() {
        return $this->hasMany(Employee::class, 'StaffCode');
    }
}
