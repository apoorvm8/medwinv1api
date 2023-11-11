<?php

namespace App\Http\Resources\API\Customer;

use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerRegisterResource extends JsonResource
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
        $customerRegisters = $this->resource->toArray();
        foreach($customerRegisters["data"] as $key => $customerRegister) {
            $customerRegister["id"] = $this->encode($customerRegister);
            $customerRegister["datetimereg"] = $customerRegister["datetimereg"] ? Carbon::parse($customerRegister["datetimereg"])->format('d/m/Y, g:i A') : null;
            $customerRegisters["data"][$key] = $customerRegister;
        }
        return $customerRegisters;
    }
}
