<?php

namespace App\Http\Resources\API\Messages;

use App\Models\CustomerMsg;
use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerMsgResource extends JsonResource
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
        $customerMsgs = $this->resource->toArray();
        $customerMsgs['unread'] = CustomerMsg::where('seen', 0)->count();
        foreach($customerMsgs["data"] as $key => $customerMsg) {
            $customerMsg["id"] = $this->encode($customerMsg);
            $customerMsg["created_at"] = $customerMsg["created_at"] ? Carbon::parse($customerMsg["created_at"])->format('d/m/Y, g:i A') : null; 
            $customerMsg["updated_at"] = $customerMsg["updated_at"] ? Carbon::parse($customerMsg["updated_at"])->format('d/m/Y, g:i A') : null; 
            $customerMsgs["data"][$key] = $customerMsg;
        }
        return $customerMsgs;
    }
}
