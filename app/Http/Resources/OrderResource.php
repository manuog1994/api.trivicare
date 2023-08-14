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
            'name' => $this->name, // 'name' => $this->name,
            'lastname' => $this->lastname, // 'lastname' => $this->lastname,
            'email' => $this->email, // 'email' => $this->email,
            'phone' => $this->phone, // 'phone' => $this->phone,
            'address' => $this->address, // 'address' => $this->address,
            'city' => $this->city, // 'city' => $this->city,
            'state' => $this->state, // 'state' => $this->state,
            'zipcode' => $this->zipcode, // 'zipcode' => $this->zipcode,
            'country' => $this->country, // 'country' => $this->country,
            'dni' => $this->dni, // 'dni' => $this->dni,
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
            'guest' => $this->whenLoaded('guest'),
            'invoice' => $this->whenLoaded('invoice'),
            'shipping' => $this->shipping,
            'token_id' => $this->token_id,
            'invoice_paper' => $this->invoice_paper,
            'note' => $this->note,
            'payment_method' => $this->payment_method,
            'track' => $this->track,
            'manual_order' => $this->manual_order,
            'pickup_point' => $this->pickup_point,
            'shipping_method' => $this->shipping_method,
        ];
    }
}
