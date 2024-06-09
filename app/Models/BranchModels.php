<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchModels extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_branch';
}

