<?php

namespace App\Http\Resources\API\Folder;

use App\Models\User;
use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    use HashIds;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth('sanctum')->user();
        $isSuperUser = null;
        if($user) {
            $isSuperUser = $user->hasRole(User::SUPER_USER);
        }
        $folder = $this->resource->toArray();
        // Date formatting
        $folder['deid'] = $isSuperUser ? $folder['id'] : null;
        $folder['id'] = $this->encode($folder);
        $folder["created_at"] = $folder["created_at"] ? Carbon::parse($folder["created_at"])->format('d/m/Y, g:i A') : '-';
        $folder["updated_at"] = $folder["updated_at"] ? Carbon::parse($folder["updated_at"])->format('d/m/Y, g:i A') : '-';
        $folder["last_downloaded_at"] = $folder["last_downloaded_at"] ? Carbon::parse($folder["last_downloaded_at"])->format('d/m/Y, g:i A') : '-';

        return $folder;
    }
}
