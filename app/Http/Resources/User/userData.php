<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class userData extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function($user){
                return[
                    'id' => $user->id,
                    'role' => [
                        'id' => $user->role->id,
                        'name' => $user->role->role
                    ],
                    'UserID' => $user->UserID,
                    // 'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile,
                    'phone' => $user->phone_number,
                ];
            })
        ];
    }
}
