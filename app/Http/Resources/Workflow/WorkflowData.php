<?php

namespace App\Http\Resources\Workflow;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Position;
use Illuminate\Support\Arr;
class WorkflowData extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($Workflow){
                $WorkflowGroup = array();
                $PositionData = DB::table('t_work_flow_position')
                ->where('WorkFlowPKID',$Workflow->PKID)
                ->select('PositionPKID')
                ->get();
                $positionTableId = array();
                foreach($PositionData as $PositionID){
                    array_push($positionTableId,$PositionID->PositionPKID);
                }
                $GroupData = DB::table('t_work_flow_group')
                ->select('GroupPKID','FlowOrder')
                ->where('WorkFlowPKID',$Workflow->PKID)->get();
                if(count($GroupData)) {
                    $WorkflowGroup = $GroupData;
                }
                return[
                    'PKID' => $Workflow->PKID,
                    'WorkFlowCode' => $Workflow->WorkFlowCode,
                    'WorkFlowName' => $Workflow->WorkFlowName,
                    'ValidationType' =>$Workflow->ValidationType,
                    'ValidationTypeId' => $Workflow->ValidationTypeId,
                    'WorkflowGroup' => $WorkflowGroup,
                    'Remark' => $Workflow->Remark,
                    'PositionPKID' => $positionTableId,
                ];
            })
        ];
    }
}
