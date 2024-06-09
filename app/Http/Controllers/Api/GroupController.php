<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupUser;
use App\Http\Resources\Group\GroupData;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function read()
    {
        $Group = Group::with('GroupUser')->get();
        return new GroupData($Group);
    }

    public function create(Request $request){
        $this->validate($request,[
            'GroupCode' => 'required',
            'GroupName' => 'required',
            'userIds' => 'required|array'
        ],[
            'GroupName.required' => 'The name field is required',
            'GroupName.regex' => 'The name field is valid',
            'userIds.required' => 'Please select at least one user',
        ]);
        //----------Modify by Lina cms-92---------
        try{
            $userPKID = Auth::user()->id;
            // insert into table t_group
            $Group = new Group();
            $Group->GroupCode = $request->GroupCode;
            $Group->GroupName = $request->GroupName;
            $Group->Remark = $request->Remark;
            date_default_timezone_set('Asia/Phnom_Penh');
            $Group->CreateDate = date('Y-m-d H:i:s');
            $Group->save();
            $userIds = $request->userIds;
            // insert into table t_group_user
            foreach($userIds as $userId) {
                $GroupUser = new GroupUser();
                $GroupUser->GroupPKID = $Group->PKID;
                $GroupUser->UserPKID = $userId;
                $GroupUser->save();
            }

            $this->InsertSysLog('GroupWorkFlow', $userPKID, 'Create', '', 'PK_ID: ' . $Group->PKID . ' Group Name: ' . $request->GroupName, 'Success', '');
            return response()->json(['message' => 'Your data created successfully'],200);

        }catch(Exception $e){
            $this->InsertSysLog('GroupWorkFlow', $userPKID, 'Create', '', 'Error: ' . $request->GroupName, 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        try{
            $userPKID = Auth::user()->id;
            // update table t_group
            $Group = Group::findOrFail($id);
            $oldGroupData = 'PK_ID: ' . $Group->PKID. ' Staff: ' .$Group->GroupName;
            $Group->GroupCode = $request->GroupCode;
            $Group->GroupName = $request->GroupName;
            $Group->Remark = $request->Remark;
            $Group->save();

            // update table t_group_user
            DB::delete('DELETE FROM t_group_user WHERE GroupPKID = ' .$id);
            $userIds = $request->userIds;
            // insert into table t_group_user
            foreach($userIds as $userId) {
                $GroupUser = new GroupUser();
                $GroupUser->GroupPKID = $Group->PKID;
                $GroupUser->UserPKID = $userId;
                $GroupUser->save();
            }
            $this->InsertSysLog('StaffPosition', $userPKID, 'Update', $oldGroupData, 'PK_ID: ' . $Group->PKID . ' Group Name: ' . $request->GroupName, 'Success', '');
            return response()->json(['message' => 'Group updated successfully'],200);

        }catch(Exception $e){
            $this->InsertSysLog('StaffPosition', $userPKID, 'Update', $oldGroupData, 'PK_ID: ' . $Group->PKID . ' Error: ' . $request->GroupName, 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function delete($id) {
        try {
            $userPKID = Auth::user()->id;
            $Group = Group::findOrFail($id);
            DB::delete('DELETE FROM t_group_user WHERE GroupPKID = ' .$id);
            $Group->delete();

            $this->InsertSysLog('GroupWorkFlow', $userPKID, 'Delete', 'PK_ID: ' . $Group->PKID . ' Group Name: ' . $Group->GroupName, '', 'Success', '');
            return response()->json(['message' => 'Group deleted successfully'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('GroupWorkFlow', $userPKID, 'Delete', 'PK_ID: ' . $Group->PKID . ' Error: ' . $Group->GroupName, '', 'Error',$e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }
}
