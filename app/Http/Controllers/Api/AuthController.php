<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\LoginInfo;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request){
        $this->validate($request, [
            'name' => 'required|max:50,min:2|string',
            'email' => 'required|email|unique:t_users,email',
            'password' => 'required|confirmed|min:8'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = $user->createToken('myapptoken')->plainTextToken;

        $user = User::where('id', $user->id)->first();
        $userData = User::select('name', 'email', 'profile', 'phone_number')->where('id', '=', $user->id)->first();
        $getdata = array();
        $getdata = Menu::whereIn('id', $user->role->menu)->orderBy('order', 'ASC')->get()->toArray();

        $responese = [
            'user' => $userData,
            'token' => $token,
            'menu' => $this->buildTree($getdata)
        ];

        return response($responese, 201);
    }
    public function login(Request $request){
        $host = gethostname();
        $rememberPassword = $request->isRememberPass;
        $this->validate($request, [
            'userId' => 'required|string|max:255',
        ]);
        $user = User::where('UserID', $request->userId)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response([
                'message' => 'Credentials not match.'
            ], 401);
        }
        //========= Theara 09/08/2023 #CMS-43 remember password========================
        
        $getInfoLogin = LoginInfo::where('DeviceName',$host)->where('UserPKID',$user->id)->first();
        if($getInfoLogin != null) {
            $getInfoLogin->Remember = $rememberPassword;
        } else {
            $getInfoLogin = new LoginInfo();
            $getInfoLogin->UserPKID = $user->id;
            $getInfoLogin->DeviceName = $host;
            $getInfoLogin->Remember = (int)$rememberPassword;
            $getInfoLogin->Password = $request->password;
        }
        $getInfoLogin->save();
        
        //==========end of login info remember password===============================
        $token = $user->createToken('token')->plainTextToken;
        $userData = User::select('id', 'UserID', 'employee_id')->where('id', '=', $user->id)->first();
        $userData['token'] = $token;
        $getdata = array();
        $getdata = Menu::whereIn('id', $user->role->menu)->orderBy('order', 'ASC')->get()->toArray();
        if($userData->employee_id == 0) {
            $employee = new Employee();
        } else {
            $employee = DB::table("t_employees")->where('id', $userData->employee_id)->first();
        }

        $this->InsertSysLog('user', $user->id, 'Login', '', 'PK_ID: ' . $user->id . ' User: ' . $employee->first_name.' '.$employee->last_name , 'Success', '');
        
        return response([
            'user' => $userData,
            'token' => $token,
            'menu' => $this->buildTree($getdata),
            'employee' => $employee,
            'getInfoLoginId' => $getInfoLogin->PKID,
        ], 201);
    }

    function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();
        $arr = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if (!empty($children)) {
                    $element['children'] = $children;
                }
                if(empty($children)){
                    $element['children'] = $arr;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
// =========== Theara #CMS-43 Remember Password=================
    public function readUserLogin($id){
        $GetLogin = DB::table('t_users')->join('t_login_info','t_users.id','t_login_info.UserPKID')
        ->select('t_users.UserID','t_login_info.Password','t_users.password','t_login_info.Remember')
        ->where('t_login_info.PKID', $id)->get();
        return response()->json(['userLogin' => $GetLogin]);
    }

    public function logout(Request $request){
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        // $this->InsertSysLog('user', $request->id, 'Log Out', '', 'PK_ID: ' . $request->id . ' User: ' . $request->first_Name, 'Success', '');
        
        // =========== Theara #CMS-43 Remember Password=================
        $GetLogin = DB::table('t_users')->join('t_login_info','t_users.id','t_login_info.UserPKID')
        ->select('t_users.UserID','t_login_info.Password','t_users.password','t_login_info.Remember')
        ->where('t_login_info.DeviceName',gethostname())->where('t_login_info.UserPKID',$request->user()->id)->where('t_login_info.Remember',true)->get();
        if($GetLogin){
            return response()->json(['Key' => $GetLogin], 200);
        }else{
            return response()->json(['Key' => $GetLogin], 200);
        }
    
    }

    public function userData(){
        return response()->json([
            'user' => User::with('role')->get()
        ]);
    }

    // ============Profile UPdate================
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:2|max:50',
            'email' => 'required|email|max:255|regex:/(.*)\.com/i|unique:t_users,email,'.$id,
        ]);

        $user = User::findOrFail($id);

        if(auth('sanctum')->user()->id != $user->id)
        {
            return response()->json(['message' => 'You not Authorized!'], 403);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        $slug = Str::slug($request->name, '-');
        if($request->hasfile('profile'))
        {
            $this->validate($request, [
                'profile' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $name = $slug.'-'.time().'-'.$request->profile->getClientOriginalName();
            \Image::make($request->profile)->save(public_path('profiles/').$name);
            $user->profile = $name;
        }

        if($request->profile){
            $url = public_path('profiles/'.$user->profile);
            if (file_exists($url)){
                unlink(public_path('profiles/'.$user->profile));
            }

            $folderPath = public_path('profiles/');
            $image_parts = explode(";base64,", $request->profile);
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = auth('sanctum')->user()->id.'-'.uniqid() . '.png';
            $imageFullPath = $folderPath.$imageName;
            file_put_contents($imageFullPath, $image_base64);
            $user->profile = $imageName;
        }

        $user->save();

        return response()->json([
            'message' => "Profile update succefully",
            'user' => $user
        ]);
    }


    public function password(Request $request, $id)
    {
        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        if(auth('sanctum')->user()->id != $user->id)
        {
            return response()->json(['message' => 'You not Authorized!'], 403);
        }

        $hashedPassword = $user->password;
        if(Hash::check($request->old_password, $hashedPassword))
        {
            if(!Hash::check($request->password, $hashedPassword))
            {
                $user = User::find($id);
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json(['message' => 'Password update Success'], 200);
            }else{
                return response()->json(['message' => 'New password cannot be the same as old password'], 200);
            }
        }else{
            return response()->json(['message' => 'Current password not match'], 200);
        }
    }
}
