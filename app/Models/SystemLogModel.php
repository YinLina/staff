<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLogModel extends Model
{
    //modify LINA 05/09/2023 #cms-91
    use HasFactory;
    protected $table = 't_sys_log';
    protected $primaryKey = 'PKID';
}
