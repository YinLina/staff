<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function read()
    {
        return Menu::with(['children' => function ($q) {
            $q->orderBy('order', 'ASC')->get();
        }])
            ->whereNull('parent_id')
            ->where('is_disable', false)
            ->orderBy('order', 'ASC')
            ->get();
    }


    public function roleMenu()
    {
        $user = User::with('role')->where('id', auth()->user()->id)->first();
        $getdata = array();
        $getdata = Menu::whereIn('id', $user->role->menu)->orderBy('order', 'ASC')->get()->toArray();
        return response($this->buildTree($getdata), 200);
    }

    public function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();
        $arr = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if (!empty($children)) {
                    $element['children'] = $children;
                }
                if (empty($children)) {
                    $element['children'] = $arr;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'role' => 'required|string|unique:roles,role',
            'description' => 'required|string',
            'menu' => 'required|array'
        ]);

        $menus = Menu::whereIn('id', $request->menu)->get();
        $getdata = array();
        foreach ($menus as $menu){
            $getMenu = $menu->parents();
            foreach ($getMenu as $item){
                $getdata[] = $item->id;
            }
        }
        $menuId = $request->menu;
        $menu = collect($getdata)->push($menuId)->flatten();

        $role = new Role();
        $role->role = $request->role;
        $role->slug = Str::slug($request->role);
        $role->menu = $menu->sort()->unique()->values()->all();
        $role->description = $request->description;

        $role->save();
        return response()->json(['message' => 'Role created successfully'], 200);
    }
}
