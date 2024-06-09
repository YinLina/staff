<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPositionModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table = 't_staff_position';

    public function position(){
        return $this->belongsTo(Position::class, 'Positions_id');
    }
}
