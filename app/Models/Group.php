<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 't_group';
    protected $primaryKey = 'PKID';

    public function GroupUser(){
        return $this->HasMany(GroupUser::class, 'GroupPKID');
    }

    public function WorkFlowGroup(){
        return $this->HasMany(WorkFlowGroup::class, 'GroupPKID');
    }
    //New
    public function validation_process(){
        return $this->HasMany(ValidationProcess::class, 'GroupPKID');
    }
}
