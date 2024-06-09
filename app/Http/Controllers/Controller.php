<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Exaption;
use App\Models\SystemLogModel;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function CreateValidationStatusTemporary(){
        $validationStatus= DB::table('t_parameter')
        ->select('ValueOne','Remark')
        ->where('ParameterCode','=','ValidationStatus')
        ->get();
        Schema::create('temp_ValidationStatus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('StatusId');
            $table->string('Status');
            $table->timestamps();
            $table->temporary();
        });
        foreach($validationStatus as $status){
            DB::table('temp_ValidationStatus')
            ->insert(['StatusId'=>intval($status->ValueOne),'Status'=>$status->Remark]);
        }
    }

    protected function CreateValidationTypeTemporary(){
        $validationType= DB::table('t_parameter')
        ->select('ValueOne','Remark')
        ->where('ParameterCode','=','ValidationType')
        ->where('ValueTwo','=',"true")
        ->get();
        Schema::create('temp_ValidationType', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ValidationTypeId');
            $table->string('ValidationType');
            $table->timestamps();
            $table->temporary();
        });
        foreach($validationType as $type){
            DB::table('temp_ValidationType')
            ->insert(['ValidationTypeId'=>intval($type->ValueOne),'ValidationType'=>$type->Remark]);
        }
    }
    
    protected function CreateHolidayTypeTemporary(){
        $holidayType= DB::table('t_parameter')
        ->select('ValueOne','TextKH')
        ->where('ParameterCode','=','HolidayType')
        ->where('ValueTwo','=',"true")
        ->get();
        Schema::create('temp_HolidayType', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('HolidayTypeId');
            $table->string('HolidayType');
            $table->timestamps();
            $table->temporary();
        });
        foreach($holidayType as $type){
            DB::table('temp_HolidayType')
            ->insert(['HolidayTypeId'=>intval($type->ValueOne),'HolidayType'=>$type->TextKH]);
        }
    }

    // Modified by Huychoung 19/09/23 #93
    protected function CreateDocumentTypeTemporary(){
        $documentType= DB::table('t_parameter')
        ->select('ValueOne','TextKH')
        ->where('ParameterCode','=','DocumentType')
        ->get();
        Schema::create('temp_DocumentType', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('DocumentTypeId');
            $table->string('DocumentType');
            $table->timestamps();
            $table->temporary();
        });
        foreach($documentType as $type){
            DB::table('temp_DocumentType')
            ->insert(['DocumentTypeId'=>intval($type->ValueOne),'DocumentType'=>$type->TextKH]);
        }
    }

    //modify LINA 01/08/2023 #cms-80
    protected function CreateAddressProvinceTemporary(){
        $province= DB::table('t_address_province')
        ->select('ProvinceCode', 'ProvinceName')
        ->get();
        Schema::create('temp_AddressProvince', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ProvinceID');
            $table->string('ProvinceName');
            $table->timestamps();
            $table->temporary();
        });
        foreach($province as $type){
            DB::table('temp_AddressProvince')
            ->insert(['ProvinceId'=>intval($type->ProvinceCode),'ProvinceName'=>$type->ProvinceName]);
        }
    }

    public static function ConvertAmount($amount, $fromPkIdCurrency, $toPkIdCurrency) {
        $fromCurrency = DB::table('t_currency')->where('PKID', $fromPkIdCurrency)->first();
        $toCurrency = DB::table('t_currency')->where('PKID', $toPkIdCurrency)->first();
        if($fromCurrency != null && $toCurrency != null) {
            if($fromCurrency->Rate == null || $fromCurrency->Rate <= 0 || $toCurrency->Rate == null || $toCurrency->Rate <= 0) {
                return $amount;
            }
            return ($amount / $fromCurrency->Rate) * $toCurrency->Rate;
        }
        return $amount;
    }

    public static function InsertSysLog($MenuName,$User_PKID,$Action,$OldData,$NewData,$LogType,$Message) {
        // $MenuName='Staff'
        // $User_PKID=1
        // $Action='Add'
        // $OldData='' for update
        // $NewData='Employee_id: 111'
        // $LogType=Error --Error/Success
        // $Message='' -- show when error
        // $HostName='msi01' -- device name
        // insert all data to t_sys_log
        try{
            $systemLog = new SystemLogModel();
            $systemLog->MenuName = $MenuName;
            $systemLog->User_PKID = $User_PKID;
            $systemLog->Action = $Action;
            $systemLog->OldData = $OldData;
            $systemLog->NewData = $NewData;
            $systemLog->LogType = $LogType;
            $systemLog->Message = $Message;
            $systemLog->HostName = gethostname();
            $systemLog->save();
            
            return $systemLog;
        }catch(Exception $e){
            echo $e;
        }
    }
}
