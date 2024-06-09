<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\userData;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{

    // ==============READ==================
    public function read()
    {
        $users = User::with(['role' => function ($q) {
            $q->select('id', 'role');
        }])->with('employee')
            ->where('id', '!=', auth('sanctum')->user()->id)
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json(['data' => $users], 200);
    }

    // =============CREATE=================
    public function create(Request $request){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'role_id' => 'required|integer',
                // 'name' => 'required|string|min:2|max:100',
                'userId' => 'required|string|unique:t_users,UserID',
                'password' => 'required|confirmed|min:6'
            ], [
                'role_id.required' => 'The user role is required.',
                'role_id.min' => 'The role number must be at least 1.',
                'role_id.max' => 'The role number must not be greater than 2.',
                'userId.required' => 'The user ID field is required.',
                'userId.unique' => 'This User Id or Phone Number is already existed.',
            ]);
            $user = new User();
            $user->role_id = $request->role_id;
            $user->UserID = $request->userId;
            // $user->UserID = $request->name;
            $user->employee_id = $request->employee_id;
            $phoneEmpty = array(['phone' => null]);
            $user->password = Hash::make($request->password);
            $slug = Str::slug($request->name, '-');
            /**if($request->hasfile('image'))
            {
                $this->validate($request, [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                $name = $slug.'-'.time().'-'.$request->image->getClientOriginalName();
                \Image::make($request->image)->save(public_path('profiles/').$name);
                $user->profile = $name;
            }
            else{
                $user->profile = 'default.png';
            }*/
            $user->save();
            $this->InsertSysLog('user', $userPKID, 'Create', '', 'PK_ID: ' . $user->id . ' UserID: ' . $request->userId, 'Success', '');
            return response()->json(['message' => 'User save succeefully.'], 200);
        } catch (Exception $showMessage) {
            $this->InsertSysLog('user', $userPKID, 'Create', '', 'Error: ' . $request->userId, 'Error', $showMessage);
            return response()->json([
                'errors' => $showMessage->errors(),
            ], 400);
        }
    }

    // ==========UDATE============
    public function update(Request $request, $id){
        try {
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'role_id' => 'required|integer',
                // 'name' => 'required|string|min:2|max:100',
                'userId' => 'required|string|unique:t_users,UserID,' . $id,
            ], [
                'role_id.required' => 'The user role is required number 1:admin and 2:User.',
                'role_id.min' => 'The role number must be at least 1.',
                'role_id.max' => 'The role number must not be greater than 2.',
                'userId.required' => 'The User ID field is required.',
                'userId.unique' => 'This User Id or Phone Number is already existed.',
            ]);
            if (auth('sanctum')->user()->role->role != 'admin') {
                return response()->json(['message' => 'You not have permission!'], 403);
            }

            $user = User::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldUserData = 'PK_ID: ' . $user->id . ' UserID: ' . $user->UserID;
            $user->role_id = $request->role_id;
            $user->UserID = $request->userId;
            // $user->UserID = $request->name;
            $user->employee_id = $request->employee_id;
            $phoneEmpty = array(['phone' => null]);
            $slug = Str::slug($request->name, '-');
            /**if($request->hasfile('image'))
            {
                $this->validate($request, [
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                $url = public_path('profiles/'.$user->profile);
                if (file_exists($url)){
                    unlink(public_path('profiles/'.$user->profile));
                }
                $name = $slug.'-'.time().'-'.$request->image->getClientOriginalName();
                \Image::make($request->image)->save(public_path('profiles/').$name);
                $user->profile = $name;
            }
            else{
                $user->profile = $user->profile;
            }*/
            $user->save();
            $this->InsertSysLog('user', $userPKID, 'Update', $oldUserData, 'PK_ID: ' . $user->id . ' Updated: ' . $request->userId, 'Success', '');
            return response()->json(['message' => 'User updated successfully.'], 200);
        } catch (Exception $showMessage) {
            $this->InsertSysLog('user', $userPKID, 'Update', 'PK_ID: ' . $user->id . ' UserID: ' . $request->userId, 'PK_ID: ' . $user->id . ' Updated: ' . $request->userId, 'Error', $showMessage);
            return response()->json([
                'errors' => $showMessage->errors(),
            ], 400);
        }
    }

    public function delete($id)
    {
        $userPKID = Auth::user()->id;
        if (auth('sanctum')->user()->role->role != 'admin') {
            return response()->json(['message' => 'You not have permission!'], 403);
        }
        // ============= Modif Theara 14/08/2023 =========================
        $userCheck = DB::table('t_users')->join('t_employees', 't_employees.id', 't_users.employee_id')->where('t_users.id', $id)->first();
        $user = User::findOrFail($id);
        if ($userCheck != null) {
            return response()->json(['checkUser' => 'This user ' . $userCheck->UserID . ' is being used.'], 200);
        }
        $user->delete();

        $this->InsertSysLog('user', $userPKID, 'Delete', 'PK_ID: ' . $user->id . ' Delete: ' . $user->UserID, '', 'Success', '');
        return response()->json(['message' => 'User data delete successfully.'], 200);
    }
}
