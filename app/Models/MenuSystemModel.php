<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSystemModel extends Model
{
    use HasFactory;
    protected $table ='t_menus';

    //------Method-------
  public function applyleave() {
        return $this->hasMany(ApplyLeave::class, 'MenuPKID');
    }


    }


