<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuSystemModel;
use App\Http\Resources\MenuSystem\MenuSystemResource;

class MenuSystemController extends Controller
{
    public function read() {
        $menuSystem= MenuSystemModel::all();
        return new MenuSystemResource($menuSystem);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_menus,name',
            'id' => 'required',
            'url' => 'required',
            'icon' => 'required',
            'type' => 'required',
            'order' => 'required',
        ],[
            'name.required' => 'The menuSystem name field is required.',
            'name.regex' => 'The menuSystem format is invalid.',
        ]);
        $menuSystem = new MenuSystemModel();
        $menuSystem->id = $request->id;
        $menuSystem->name= $request->name;
        $menuSystem->url = $request->url;
        $menuSystem->parent_id = $request->parent_id;
        $menuSystem->icon=$request->icon;
        $menuSystem->type=$request->type;
        $menuSystem->order=$request->order;
        $menuSystem->is_disable = $request->is_disable;
        $menuSystem->save();
        return response()->json(['message' => 'MenuSystem created successfully'], 200);
    }
    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_menus,name,'.$id.',id',
            'id' => 'required',
            'url' => 'required',
            'icon' => 'required',
            'type' => 'required',
            'order' => 'required',
        ],[
            'name.required' => 'The menuSystem name field is required.',
            'name.regex' => 'The menuSystem format is invalid.',
        ]);
        $menuSystem = MenuSystemModel::findOrFail($id);
        $menuSystem->id = $request->id;
        $menuSystem->name= $request->name;
        $menuSystem->url = $request->url;
        $menuSystem->parent_id = $request->parent_id;
        $menuSystem->icon=$request->icon;
        $menuSystem->type=$request->type;
        $menuSystem->order=$request->order;
        $menuSystem->is_disable = $request->is_disable;
        $menuSystem->save();
        return response()->json(['message' => 'MenuSystem updated successfully'], 200);
    }
    public function delete($id){
        $parameter = MenuSystemModel::findOrFail($id);
        $parameter->delete();
        return response()->json(['message' => 'MenuSystem deleted successfully.'], 200);
    }
    }



