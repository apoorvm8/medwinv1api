<?php

namespace App\Http\Resources\API\Customer;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $customers = $this->resource->toArray();
        foreach($customers["data"] as $key => $customer) {
            $customer["address"] = $customer["subadd1"] . "<br>" . $customer["subadd2"] . "<br>" . $customer["subadd3"];
            $customer["installdate"] = Carbon::parse($customer["installdate"])->format('d/m/Y');
            $customer["nextamcdate"] = Carbon::parse($customer["nextamcdate"])->format('d/m/Y');
            $customers["data"][$key] = $customer;
        }
        return $customers;
    }
}
