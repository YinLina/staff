<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResignationReason\ResignationReasonResource;
use App\Models\ResignationReasonModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ResignationReasonController extends Controller
{
    public function read() {
        $resignationReason = ResignationReasonModel::all();
        return new ResignationReasonResource($resignationReason);
    }

    public function create(Request $request) {
        try{
            $userPKID = Auth::user()->id;

            $this->validate($request, [
                'ReasonCode' => 'required|string|min:2|max:255|unique:t_resignation_reason,ReasonCode',
                'Description' => 'required',
            ],[
                'ReasonCode.required' => 'Reason code is required',
                'Description.required' => 'Description is required',
            ]);
           
            $resignationReason = new ResignationReasonModel();
            $resignationReason->ReasonCode = $request->ReasonCode;
            $resignationReason->Description = $request->Description;
            $resignationReason->save();
            
            $this->InsertSysLog('ResignationReason', $userPKID, 'Create', '', 'PKID: ' . $resignationReason->PKID . ' ReasonCode: ' . $request->ReasonCode, 'Success', '');
            return response()->json(['message' => 'Resignation reason created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('ResignationReason', $userPKID, 'Create', '', 'ResignationReason Error: ' . $request->ReasonCode, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'ReasonCode' => 'required|string|min:2|max:255|unique:t_resignation_reason,ReasonCode',
                'Description' => 'required',
            ],[
                'ReasonCode.required' => 'Reason code is required',
                'Description.required' => 'Description is required',
            ]);
            $resignationReason = ResignationReasonModel::findOrFail($id);
            $oldResignationReasonData = 'PKID: ' . $resignationReason->PKID. ' ReasonCode: ' .$resignationReason->ReasonCode;
            $resignationReason->ReasonCode = $request->ReasonCode;
            $resignationReason->Description = $request->Description;
            $resignationReason->save();

            $this->InsertSysLog('ResignationReason', $userPKID, 'Update', $oldResignationReasonData, 'PK_ID: ' . $resignationReason->PKID . ' ReasonCode: ' . $request->ReasonCode, 'Success', '');
            return response()->json(['message' => 'Resignation reason created successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('ResignationReason', $userPKID, 'Update', $oldResignationReasonData, 'Error: ' . $request->ReasonCode. ' Error input fields', 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function delete ($id) {
        try {
            $userPKID = Auth::user()->id;
            $resignationReason = ResignationReasonModel::findOrFail($id);
            $resignationReason->delete();
            return response()->json(['message' => 'Resignation reason deleted successfully.'], 200);
        } catch (Exception $e){
            $this->InsertSysLog('ResignationReason', $userPKID, 'Delete', 'PKID: ' . $resignationReason->PKID . ' ResignationReason Error: ' . $resignationReason->ReasonCode, '', 'Error',$e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }

}
