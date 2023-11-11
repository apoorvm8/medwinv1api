<?php

namespace App\Http\Resources\API\Customer;

use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWhatsappResource extends JsonResource
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
        $customerWhatsapps = $this->resource->toArray();
        foreach($customerWhatsapps["data"] as $key => $customerWhatsapp) {
            $customerWhatsapp["id"] = $this->encode($customerWhatsapp);
            $customerWhatsapp["install_date"] = $customerWhatsapp["install_date"] ? Carbon::parse($customerWhatsapp["install_date"])->format('d/m/Y') : null;
            $customerWhatsapp["next_amc_date"] = $customerWhatsapp["next_amc_date"] ? Carbon::parse($customerWhatsapp["next_amc_date"])->format('d/m/Y') : null; 
            $customerWhatsapp["created_at"] = $customerWhatsapp["created_at"] ? Carbon::parse($customerWhatsapp["created_at"])->format('d/m/Y, g:i A') : null; 
            $customerWhatsapps["data"][$key] = $customerWhatsapp;
        }
        return $customerWhatsapps;
    }
}
