<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_profile_id' => $this->user_profile_id,
            'products' => $this->products,
            'total' => $this->total,
            'coupon' => $this->coupon,
            'order_date' => $this->order_date,
            'paid' => $this->paid,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'user_profile' => $this->whenLoaded('user_profile'),
        ];
    }
}
