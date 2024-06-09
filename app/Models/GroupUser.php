<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = array('GroupPKID', 'UserPKID', 'created_at', 'updated_at');
    protected $table = 't_group_user';

    public function group(){
        return $this->belongsTo(Group::class, 'GroupPKID');
    }

    public function users(){
        return $this->belongsTo(User::class, 'UserPKID');
    }
}
