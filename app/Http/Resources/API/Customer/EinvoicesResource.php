<?php

namespace App\Http\Resources\API\Customer;

use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EinvoicesResource extends JsonResource
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
        $einvoices = $this->resource->toArray();
        foreach($einvoices["data"] as $key => $einvoice) {
            $einvoice["id"] = $this->encode($einvoice);
            $einvoice["install_date"] = $einvoice["install_date"] ? Carbon::parse($einvoice["install_date"])->format('d/m/Y') : null;
            $einvoice["next_amc_date"] = $einvoice["next_amc_date"] ? Carbon::parse($einvoice["next_amc_date"])->format('d/m/Y') : null; 
            $einvoice["created_at"] = $einvoice["created_at"] ? Carbon::parse($einvoice["created_at"])->format('d/m/Y, g:i A') : null; 
            $einvoices["data"][$key] = $einvoice;
        }
        return $einvoices;
    }
}
