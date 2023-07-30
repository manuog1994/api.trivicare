<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
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
            'product_id' => $this->product_id,
            'model' => $this->model,
            'color' => $this->color,
            'size' => $this->size,
            'image' => $this->whenLoaded('image'),
            'product' => $this->whenLoaded('product'),
            'stock' => $this->stock,
        ];
    }
}
