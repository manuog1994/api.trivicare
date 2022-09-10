<?php

namespace App\Http\Resources;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'price' => $this->price,
            'stock' => $this->stock,
            'barcode' => $this->barcode,
            'slug' => $this->slug,
            'sold' => $this->sold,
            //'review' => ReviewResouce::make($this->whenLoaded('review')),
            'discount' => $this->discount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status == 1 ? 'Borrador' : 'Publicado',
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
