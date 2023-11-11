<?php

namespace App\Http\Resources\API\User;

use Hashids\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hashIds = new Hashids('', config('app.hash_min_length'));
        return [
            'id' => $hashIds->encode($this->id),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'role' => isset($this->resource->getRoleNames()[0]) ? $this->resource->getRoleNames()[0] : 'user',
            'permissions' => $this->resource->getPermissionsViaRoles()->pluck('name')
        ];
    }
}
