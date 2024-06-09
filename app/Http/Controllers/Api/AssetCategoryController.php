<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetCategoryModel;
use App\Http\Resources\AssetCategory\AssetCategoryResources;

class AssetCategoryController extends Controller
{
    public function read() { 
        $AssetCategory = AssetCategoryModel::all();
        return new AssetCategoryResources($AssetCategory);
    }
    public function create(Request $request) {
        $this->validate($request, [
            'AssetCategory' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_asset_category,AssetCategory',
        ],[
            'AssetCategory.required' => 'The asset category name field is required.',
        ]);
        $AssetCategory = new AssetCategoryModel();
        $AssetCategory->AssetCategory = $request->AssetCategory;
        $AssetCategory->Remark = $request->Remark;
        $AssetCategory->save();
        return response()->json(['message' => 'Asset Category created successfully'], 200);
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'AssetCategory' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_asset_category,AssetCategory,'.$id.',PKID',
        ],[
            'AssetCategory.required' => 'The asset category name field is required.',
        ]);
        $AssetCategory = AssetCategoryModel::findOrFail($id);
        $AssetCategory->AssetCategory = $request->AssetCategory;
        $AssetCategory->Remark = $request->Remark;
        $AssetCategory->save();
        return response()->json(['message' => 'Asset Category created successfully'], 200);
    }
    public function delete($id){
        $AssetCategory = AssetCategoryModel::findOrFail($id);
        $AssetCategory->delete();
        return response()->json(['message' => 'Asset Category deleted successfully.'], 200);
    }
}


