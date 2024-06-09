<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParameterModel;
use App\Http\Resources\Parameter\ParameterResource;
use App\Http\Resources\ValidationTypeData\ValidationType;
use Exception;
use Illuminate\Support\Facades\Auth;

class ParameterController extends Controller
{
    public function read() {
        $parameter = ParameterModel::all();
        return new ParameterResource($parameter);
    }
    public function GetValidationType(){
        $this->CreateValidationTypeTemporary();
        $ValidationType = DB::table('temp_ValidationType')->get();
        schema::drop('temp_ValidationType');
        return new ValidationType($ValidationType);
    }
    public function ReadParameter(){
        $parameterData = ParameterModel::where('ParameterCode','StaffCodeFormat')->get();
        return response()->json(['ParameterData' => $parameterData]);
    }
    public function create(Request $request){
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request,[
                'parameterCode' => 'required',//|string|min:2|max:255|:t_parameter,ParameterCode
                'ValueOne' => 'required', //|string|max:255|:t_parameter,ValueOne
            ],[
                'parameterCode.required' => 'The name field is required.',
            ]);
            $parameter = new ParameterModel();
            $parameter->parameterCode = $request->parameterCode;
            $parameter->ValueOne = $request->ValueOne;
            $parameter->ValueTwo = $request->ValueTwo;
            $parameter->Remark = $request->Remark;
            $parameter->TextKH = $request->textKH;
            $parameter->TextEN = $request->textEN;
            $parameter->TextVN = $request->textVN;
            $parameter->TextTH = $request->textTH;
            $parameter->TextCH = $request->textCH;
            $parameter->TextFR = $request->textFR;
            $parameter->save();

            $this->InsertSysLog('parameter', $userPKID, 'Create', '', 'PK_ID: ' . $parameter->PKID . ' ParameterCode: ' . $request->parameterCode, 'Success', '');
            return response()->json(['message' => 'Parameter created successfully'], 200);

        }catch(Exception $e){
            $this->InsertSysLog('parameter', $userPKID, 'Create', '', 'ParameterCode Error: ' . $request->parameterCode, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        }
    }
    public function update(Request $request, $id) {
        try{
            $userPKID = Auth::user()->id;
            $this->validate($request, [
                'parameterCode' => 'required', //|string|min:2|max:255|:t_parameter,ParameterCode,'.$id.',PKID
                ],[
                    'parameterCode.required' => 'The parameter name field is required.',
                    'parameterCode.regex' => 'The parameter name format is invalid.',
            ]);
            $parameter = ParameterModel::findOrFail($id);
            $oldParameterData = 'PK_ID: ' . $parameter->PKID. ' ParameterCode: ' .$parameter->ParameterCode;
            $parameter->parameterCode = $request->parameterCode;
            $parameter->ValueOne = $request->ValueOne;
            $parameter->ValueTwo = $request->ValueTwo;
            $parameter->Remark = $request->Remark;
            $parameter->TextKH = $request->textKH;
            $parameter->TextEN = $request->textEN;
            $parameter->TextVN = $request->textVN;
            $parameter->TextTH = $request->textTH;
            $parameter->TextCH = $request->textCH;
            $parameter->TextFR = $request->textFR;
            $parameter->save();

            $this->InsertSysLog('parameter', $userPKID, 'Update', $oldParameterData, 'PK_ID: ' . $parameter->PKID . ' ParameterCode: ' . $request->parameterCode, 'Success', '');
            return response()->json(['message' => 'Parameter updated successfully'], 200);
        }catch(Exception $e){
            $this->InsertSysLog('parameter', $userPKID, 'Update', $oldParameterData, 'PK_ID: ' . $parameter->PKID . ' ParameterCode Error: ' . $request->parameterCode, 'Error', $e);
            return response()->json([
                'errors' => $e->errors(),
            ], 400);
        } 
    }
    public function delete($id){
        try{
            $userPKID = Auth::user()->id;
            $parameter = parameterModel::findOrFail($id);
            $parameter->delete();

            $this->InsertSysLog('parameter', $userPKID, 'Delete', 'PK_ID: ' . $parameter->PKID . ' ParameterCode: ' . $parameter->ParameterCode, '', 'Success', '');
            return response()->json(['message' => 'Parameter deleted successfully.'], 200);
        } catch (Exception $e) {
            $this->InsertSysLog('parameter', $userPKID, 'Delete', 'ParameterCode Error: ' . $parameter->ParameterCode, '', 'Error',$e);
            return response()->json([
                'Error' => $e->getMessage(),
            ], 400);
        }
    }
}
