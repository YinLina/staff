<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowGroup extends Model
{
    use HasFactory;
    protected $table = 't_work_flow_group';
    protected $primaryKey = 'WorkFlowPKID';
    
    public function Workflow(){
        return $this->belongsTo(WorkflowGroup::class, 'WorkFlowPKID');
    }

    public function Group(){
        return $this->belongsTo(Group::class, 'GroupPKID');
    }
}
