<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 't_roles';
    protected $casts = [
        'menu' => 'array',
        'menu_selected' => 'array',
    ];

    public function user(){
        return $this->hasMany(User::class, 'role_id');
    }

    public function menu(){
        return $this->hasMany(Menu::class, 'id')->with('menu');
    }
}
