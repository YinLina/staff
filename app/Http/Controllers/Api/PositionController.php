<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Position\PositionData;
use App\Http\Resources\PositionData\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PositionController extends Controller
{
    public function read()
    {
        return new PositionData(Position::with('department', 'staffPosition')->get());
    }

    public function create(Request $request){
        $this->validate($request, [
            'title' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_positions,title',
        ],[
            'title.required' => 'The Position name field is required.',
            'title.regex' => 'The Position name format is invalid.',
        ]);
        //----------Modify by Lina cms-92---------
        try{
            $userPKID = Auth::user()->id;
            $position = new Position();
            $position->title = $request->title;
            $position->ParentPositions_id = $request->ParentPositions_id;
            $position->Department_PKID = $request->Department_PKID;
            $position->Remark = $request->Remark;
            $position->save();
            
            $this->InsertSysLog('position', $userPKID, 'Create', '', 'PK_ID: ' . $position->id . ' Position: ' . $request->title, 'Success', '');
            return response()->json(['message' => 'Position created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('position', $userPKID, 'Create', '', 'Position Error: ' . $request->title, 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'title' => 'required|string|min:2|regex:/[a-zA-Z]+$/u|max:255|unique:t_positions,title,'.$id,
        ],[
            'title.required' => 'The Position name field is required.',
        ]);
        //----------Modify by Lina cms-92---------
        try{
            $userPKID = Auth::user()->id;
            $position = Position::findOrFail($id);
            //modify by LINA 13/09/2023 #cms-92
            $oldPositionData = 'PK_ID: ' . $position->id. ' Position: ' .$position->title;
            $position->title = $request->title;
            $position->ParentPositions_id = $request->ParentPositions_id;
            $position->Department_PKID = $request->Department_PKID;
            $position->Remark = $request->Remark;
            $position->save();
            
            $this->InsertSysLog('position', $userPKID, 'Update', $oldPositionData, 'PK_ID: ' . $position->id . ' Position: ' . $request->title, 'Success', '');
            return response()->json(['message' => 'Position updated successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('position', $userPKID, 'Update','PK_ID: ' . $position->id . ' Position: ' .$request->title, 'PK_ID: ' . $position->id . ' Position Error: ' . $request->title. ' Error position fields', 'Error', $e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    // Modified by huychoung <<Validate cannot delete position if it is being used as ParentPosition>> 
    public function delete($id){
        $userPKID = Auth::user()->id;
        $position = Position::findOrFail($id);
        $parentPositions = DB::table('t_positions')
                    ->where('ParentPositions_id', $position->id)
                    ->get();            
        if(count($parentPositions) > 0) {
            return response()->json([ 'message' => 'Cannot deleted' ]);
        }
        $position->delete();

        $this->InsertSysLog('position',$userPKID,'Delete','PK_ID: '.$position->id .' Delete: '.$position->title,'','Success','');
        return response()->json([ 'message' => 'Position deleted successfully.' ], 200);
    }
}
