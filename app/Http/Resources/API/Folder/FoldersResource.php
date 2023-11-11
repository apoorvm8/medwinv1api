<?php

namespace App\Http\Resources\API\Folder;

use App\Models\User;
use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FoldersResource extends JsonResource
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
        $folders = $this->resource->toArray();
        $userRole = isset(auth('sanctum')->user()->getRoleNames()[0]) ? auth('sanctum')->user()->getRoleNames()[0] : 'user';

        $validFolders = [];
        if(count($folders) > 0) {
            foreach($folders as $key => $folder) {
                $folder["folder_permissions"] = isset($folder["folder_permissions"]) && count($folder["folder_permissions"]) > 0 ? $folder["folder_permissions"][0] : null;
                if($userRole == User::SUPER_USER || $folder["slug"] == "root" || ($folder["folder_permissions"] && $folder["folder_permissions"]["view"] == true)) {
                    $folder['id'] = $this->encode($folder);
                    $folder["parent_id"] = $this->encode($folder, "parent_id");
                    $folder["is_open"] = false;
                    if($folder["resource_type"] == "folder") {
                        $folder["margin_left"] = $folder["depth"] * 1 . 'rem';
                    } else {
                        $folder["margin_left"] = (($folder["depth"] * 1) + 1.3) . 'rem';
                    }
    
                    // Date formatting
                    $folder["created_at"] = $folder["created_at"] ? Carbon::parse($folder["created_at"])->format('d/m/Y, g:i A') : '-';
                    $validFolders[] = $folder;
                } 
            }
            return $validFolders;
        } else {
            return [];
        }
    }
}
