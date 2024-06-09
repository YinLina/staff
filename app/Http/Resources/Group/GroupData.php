<?php

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\User;

class GroupData extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'data' => $this->collection->transform(function($group){
                $userArr = array();
                $userNamesArr = array();
                $userIdsArr = array();
                $userNames = "";
                foreach($group->GroupUser as $groupUser) {
                    $user = User::find($groupUser->UserPKID);
                    if($user != null) {
                        $userModel = new UserModel();
                        $userModel->id = $user->id;
                        $userModel->name = $user->UserID;
                        array_push($userArr, $userModel);
                        array_push($userNamesArr, $userModel->name);
                        array_push($userIdsArr, $userModel->id);
                    }
                }
                $userNames = implode(', ', $userNamesArr);
                return
                [
                    'PKID' => $group->PKID,
                    'GroupCode' => $group->GroupCode,
                    'GroupName' => $group->GroupName,
                    'CreateDate' => $group->CreateDate,
                    'Remark' => $group->Remark,
                    'GroupUser' => $userArr,
                    'UserNames' => $userNames,
                    'UserIds' => $userIdsArr
                ];
            })
        ];
    }
}

class UserModel {
    public $id;
    public $name;
}
