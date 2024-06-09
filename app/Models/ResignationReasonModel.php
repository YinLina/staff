<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationReasonModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_resignation_reason';
}
