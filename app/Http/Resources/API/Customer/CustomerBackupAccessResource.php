<?php

namespace App\Http\Resources\API\Customer;

use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerBackupAccessResource extends JsonResource
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
        $customerBackups = $this->resource->toArray();
        foreach($customerBackups["data"] as $key => $customerBackup) {
            $customerBackup["id"] = $this->encode($customerBackup);
            $customerBackup["install_date"] = $customerBackup["install_date"] ? Carbon::parse($customerBackup["install_date"])->format('d/m/Y') : null;
            $customerBackup["next_amc_date"] = $customerBackup["next_amc_date"] ? Carbon::parse($customerBackup["next_amc_date"])->format('d/m/Y') : null; 
            $customerBackup["created_at"] = $customerBackup["created_at"] ? Carbon::parse($customerBackup["created_at"])->format('d/m/Y, g:i A') : null; 
            $customerBackups["data"][$key] = $customerBackup;
        }
        return $customerBackups;
    }
}
