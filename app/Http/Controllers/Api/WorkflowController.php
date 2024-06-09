<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\WorkflowGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Workflow\WorkflowData;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use App\Models\WorkflowPosition;
use App\Http\Resources\PositionData\PositionResource;
use Exception;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function read(){
    // Get all workflow
        $this->CreateValidationTypeTemporary();
        $workflow = DB::table('t_work_flow')
        ->join('temp_ValidationType','temp_ValidationType.ValidationTypeId','t_work_flow.ValidationType')
        ->select('PKID','WorkFlowCode','WorkFlowName','Remark','temp_ValidationType.ValidationType','temp_ValidationType.ValidationTypeId')
        ->get();
        Schema::drop('temp_ValidationType');
        return new WorkflowData($workflow);
    }
    // Get Position 
    public function readPosition($id){
        $Work = DB::table('t_positions')
        ->whereNotIn('id', function($query) use($id){
            $query->select('PositionPKID')->from('t_work_flow_position')
            ->join("t_work_flow","t_work_flow_position.WorkFlowPKID","t_work_flow.PKID")
            ->where("ValidationType",$id);
        })->get();
        return response()->json(['positions' => $Work]);
    }
    // Read and Update Position
    public function UpdatePosition($id){
        //check
        $tempExistPosition = array();
        $existPosition = DB::table('t_work_flow_position')
        ->join('t_positions','t_work_flow_position.PositionPKID','t_positions.id')
        ->where('WorkFlowPKID',$id)->select('id','title')
        ->get()->toArray();
        foreach($existPosition as $data) {
            $positionModel = new PositionModel();
            $positionModel->id = $data->id;
            $positionModel->title = $data->title;
            $positionModel->isChecked = true;
            array_push($tempExistPosition, $positionModel);
        }
        //not check
        $tempNotExistPosition = array();
        $validationtype=DB::table('t_work_flow')->where('PKID',$id)->select('ValidationType')->first();
        $value=$validationtype->ValidationType;
        $notexistPosition = DB::table('t_positions')
        ->whereNotIn('id', function($query) use($id,$value){
            $query->select('PositionPKID')->from('t_work_flow_position')
            ->join("t_work_flow","t_work_flow_position.WorkFlowPKID","t_work_flow.PKID")
            ->where("ValidationType",$value);
        })->select('id','title')->get();
        foreach($notexistPosition as $data) {
            $positionModel = new PositionModel();
            $positionModel->id = $data->id;
            $positionModel->title = $data->title;
            $positionModel->isChecked = false;
            array_push($tempNotExistPosition, $positionModel);
        }
        $notexistPosition = array_merge($tempNotExistPosition,$tempExistPosition);
        return response()->json(['positionData' => $notexistPosition]);
    }
    
    public function create(Request $request){
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request,[
                'WorkFlowCode' => 'required',
                'WorkFlowName' => 'required',
                'ValidationTypeID' => 'required',
                'positionId' => 'required',
                'dataFormTable' => 'required',
            ],[
                'WorkFlowCode.required' => 'The name field is required.',
                'WorkFlowName.regex' => 'The name field is invalid.',
                'ValidationTypeID.required' => 'The name field is required.',
                'positionId.required' => 'Please select a position.',
            ]);
            //Workflow group
            $Group = $request->dataFormTable;
            foreach($Group as $group){
                $filteredArray = Arr::where($Group, function ($value, $key) use( $group) {
                    return $value['groupId'] == $group['groupId'];
                });
                if($group['groupId'] == 0){
                    $this->validate($request, [
                        'groupId' => 'required',
                    ],[
                        'groupId.required' => 'Data is not empty.'
                    ]);
                }
                if(count($filteredArray) > 1){
                    $this->validate($request, [
                        'groupId' => 'required',
                    ],[
                        'groupId.required' => 'Data is duplicate.'
                    ]);
                }
    
            }
            //Workflow
            $workflow = new Workflow();
            $workflow->WorkFlowCode = $request->WorkFlowCode;
            $workflow->WorkflowName = $request->WorkFlowName;
            $workflow->ValidationType = $request->ValidationTypeID;
            $workflow->Remark = $request->Remark;
            date_default_timezone_set('Asia/Phnom_Penh');
            $workflow->CreateDate = date("Y-m-d H:i:s");
            $workflow->save();
            //Workflow group
            foreach($Group as $group){
                $WorkflowGroup = new WorkflowGroup();
                $WorkflowGroup->WorkFlowPKID = $workflow->PKID;
                $WorkflowGroup->GroupPKID = $group["groupId"];
                $WorkflowGroup->FlowOrder = $group["order"];
                $WorkflowGroup->save();
            }
            //Workflow position
            $position = $request->positionId;
            foreach($position as $positionData){
                $WorkflowPosition = new WorkflowPosition();
                $WorkflowPosition->WorkFlowPKID = $workflow->PKID;
                $WorkflowPosition->PositionPKID = $positionData;
                $WorkflowPosition->save();
            }
            $this->InsertSysLog('WorkFlow', $userPKID, 'Create', '', 'PK_ID: ' . $workflow->PKID . ' WorkFlow Code: ' . $request->WorkFlowCode, 'Success', '');
            return response()->json(['message' => 'Your data saved successfully.'],200);

        }catch(Exception $e){
            $this->InsertSysLog('WorkFlow', $userPKID, 'Create', '', 'Error: ' . $request->WorkFlowCode, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete(Request $request, $id){
        try{
            $userPKID = Auth::user()->id;
            $workflow = Workflow::findOrFail($id);
            DB::delete('DELETE FROM t_work_flow_group WHERE WorkFlowPKID = '.$id);
            DB::delete('DELETE FROM t_work_flow_position WHERE WorkFlowPKID = '.$id);
            $workflow->delete();

            $this->InsertSysLog('WorkFlow', $userPKID, 'Delete', 'PK_ID: ' . $workflow->PKID . ' WorkFlow Code: ' . $workflow->WorkFlowCode, '', 'Success', '');
            return response()->json(['message' => 'Your data deleted successfully.'], 200);

        }catch(Exception $e){
            $this->InsertSysLog('WorkFlow', $userPKID, 'Delete', 'PK_ID: ' . $workflow->PKID . ' WorkFlow Error: ' . $workflow->WorkFlowCode, '', 'Error',$e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id){
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request,[
                'WorkFlowCode' => 'required',
                'WorkFlowName' => 'required|string|max:100|unique:t_work_flow,WorkFlowName,'.$id.',PKID',
                'ValidationTypeID' => 'required',
                'positionId' => 'required',
            ],[
                'WorkFlowName.required' => 'The name field is required.',
                'WorkFlowName.regex' => 'The name field is in valid.',
                'positionId.required' => 'Please select a position'
            ]);
            $workflow = Workflow::findOrFail($id);
            $oldWorkFlowData = 'PK_ID: ' . $workflow->PKID. ' WorkFlowCode: ' .$workflow->WorkFlowCode;
            $workflow->WorkFlowCode = $request->WorkFlowCode;
            $workflow->WorkflowName = $request->WorkFlowName;
            $workflow->ValidationType = $request->ValidationTypeID;
            $workflow->Remark = $request->Remark;
            $workflow->save();
            
            DB::delete('DELETE FROM t_work_flow_group WHERE WorkFlowPKID = '.$id);
            $Group = $request->dataFormTable;
            foreach($Group as $group){
                $WorkflowGroup = new WorkflowGroup();
                $WorkflowGroup->WorkFlowPKID = $workflow->PKID;
                $WorkflowGroup->GroupPKID = $group["groupId"];
                $WorkflowGroup->FlowOrder = $group["order"];
                $WorkflowGroup->save();
            }
            DB::delete('DELETE FROM t_work_flow_position WHERE WorkFlowPKID = '.$id);
            $position = $request->positionId;
            foreach($position as $positionData){
                $WorkflowPosition = new WorkflowPosition();
                $WorkflowPosition->WorkFlowPKID = $workflow->PKID;
                $WorkflowPosition->PositionPKID = $positionData;
                $WorkflowPosition->save();
            }

            $this->InsertSysLog('WorkFlow', $userPKID, 'Update', $oldWorkFlowData, 'PK_ID: ' . $workflow->PKID . ' WorkFlow Code: ' . $request->WorkFlowCode, 'Success', '');
            return response()->json(['message' => 'Your data updated successfully.'],200);

        }catch(Exception $e){
            $this->InsertSysLog('WorkFlow', $userPKID, 'Update','PK_ID: ' . $workflow->PKID . ' Staff: ' .$request->Employees_id, 'PK_ID: ' . $workflow->PKID . ' Error: ' . $request->WorkFlowCode, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
}

class PositionModel {
    public $id;
    public $title;
    public $isChecked;
}

