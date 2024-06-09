<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationModel extends Model
{
    use HasFactory;
    protected $table = 't_validation_status';
    protected $primaryKey = 'PKID';

    public function ValidationStatus() {
        return $this->hasMany(ApplyLeave::class, 'Status');
    }
}
