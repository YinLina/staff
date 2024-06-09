<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SystemLog\SystemLogResource;
use App\Models\SystemLogModel;

class SystemLogController extends Controller
{
    //modify LINA 06/09/2023 #cms-91
    public function read() {
        $systemLog = SystemLogModel::all();
        return new SystemLogResource($systemLog);
    }
}
