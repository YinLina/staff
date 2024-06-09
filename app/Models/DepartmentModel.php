<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentModel extends Model
{
    // Modified by Huychoung 04/08/2023 #84
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_department';

    public function position() {
        return $this->hasMany(Position::class, 'Department_PKID');
    }
}
