<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(is_null($this->name)){
            return "No hay etiquetas";
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'slug' => $this->slug,
            'color' => $this->color,
        ];
    }
}
