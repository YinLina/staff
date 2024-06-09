<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategoryModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'PKID';
    protected $table ='t_asset_category';
}
