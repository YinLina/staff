<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 't_menus';
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->with('children')
            ->orderBy('order', 'ASC')
            ->where('is_disable', false);
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'menu')->with('children');
    }

    // =========================================================
    public function parents()
    {
        $parents = $this->parent()->get();
        while ($parents->last() && $parents->last()->parent_id !== null) {
            $parent = $parents->last()->parent()->get();
            $parents = $parents->merge($parent);
        }
        return $parents;
    }

    public function ancestors()
    {
        $ancestors = $this->where('id', '=', $this->parent_id)->get();
        while ($ancestors->last() && $ancestors->last()->parent_id !== null) {
            $parent = $this->where('id', '=', $ancestors->last()->parent_id)->get();
            $ancestors = $ancestors->merge($parent);
        }
        return $ancestors;
    }
}
