<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read()
    {
        $roles = Role::with('user')->get();
        $getdata = array();
        foreach ($roles as $role){
            $getdata[] = array(
                'role' => [
                    'id' => $role->id,
                    'name' => $role->role,
                    'description' => $role->description,
                    'menu' => $role->menu_selected,
                ],
                'menu' => $this->buildTree(Menu::whereIn('id', $role->menu)->get()->toArray()),
                'menu_count' => Menu::whereIn('id', $role->menu)->count(),
                'user' => $role->user,
            );
        }
        return response()->json($getdata, 200);
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

    public function role(){
        $roles = Role::select('id', 'role')->get();
        return response()->json(['data' => $roles], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request,[
                'role' => 'required|string|unique:t_roles,role',
                'description' => 'required|string',
                'menu' => 'required|array'
            ],
            [
                'menu.required' => 'Please select any menu.',
            ]);
            // =============check avalibale menu==============
                $menu_is_avalible = array();
                foreach($request->menu as $menu){
                    $menu_is_avalible[] = Menu::where('id', '=', $menu)->pluck('id');
                }
    
                foreach ($menu_is_avalible as $item){
                    if(count($item) == 0)
                    return response()->json(['message' => 'please select avaliable menu.']);
                }
            // =============/check avalibale menu==============
    
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
            $role->menu_selected = $menuId;
            $role->description = $request->description;
            $role->save();

            $this->InsertSysLog('role', $userPKID, 'Create', '', 'PK_ID: ' . $role->id . ' Role: ' . $request->role, 'Success', '');
            return response()->json(['message' => 'Role created successfully'], 200);

        }catch(Exception $e){
            $this->InsertSysLog('role', $userPKID, 'Create', '', 'Role Error: ' . $request->role, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request,[
                'role' => 'required|string|unique:t_roles,role,'.$id,
                'description' => 'required|string',
                'menu' => 'required|array'
            ],
            [
                'menu.required' => 'Please select any menu.',
            ]);
            // =============check avalibale menu==============
            $menu_is_avalible = array();
            foreach($request->menu as $menu){
                $menu_is_avalible[] = Menu::where('id', '=', $menu)->pluck('id');
            }

            foreach ($menu_is_avalible as $item){
                if(count($item) == 0)
                return response()->json(['message' => 'please select avaliable menu.']);
            }
            // =============/check avalibale menu==============
    
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
    
            $role = Role::findOrFail($id);
            $oldRoleData = 'PK_ID: '.$role->id .' Role: '.$role->role;
            $role->role = $request->role;
            $role->slug = Str::slug($request->role);
            $role->menu = $menu->sort()->unique()->values()->all();
            $role->menu_selected = $menuId;
            $role->description = $request->description;
            $role->save();

            $this->InsertSysLog('role', $userPKID, 'Update', $oldRoleData, 'PK_ID: ' . $role->id . ' role: ' . $request->role, 'Success', '');
            return response()->json(['message' => 'Role updated successfully'], 200);

        }catch(Exception $e){
            $this->InsertSysLog('role', $userPKID, 'Update', $oldRoleData, 'PK_ID: ' . $role->id . ' Role Error: ' . $request->role, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id){
        try{
            $userPKID = Auth::user()->id;
            if(auth('sanctum')->user()->role->role != 'admin')
            {
                return response()->json(['message' => 'You not have permission!'], 403);
            }
            $role = Role::findOrFail($id);
            $role->delete();

            $this->InsertSysLog('role', $userPKID, 'Delete', 'PK_ID: ' . $role->id . ' Role: ' . $role->role, '', 'Success', '');
            return response()->json([ 'message' => 'Role data deleted successfully.' ], 200);

        } catch (Exception $e) {
            $this->InsertSysLog('role', $userPKID, 'Delete', 'PK_ID: ' . $role->id . ' Role Error: ' . $role->role, '', 'Error',$e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }
}
