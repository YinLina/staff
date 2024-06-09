<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;
    protected $table = 't_work_flow';
    protected $primaryKey = 'PKID';
    public function WorkflowGroup(){
        return $this->hasMany(WorkflowGroup::class, 'WorkFlowPKID');
    }
}
