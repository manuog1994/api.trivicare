<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class Variation extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [ 'id', 'model', 'color', 'size' ];

    // Relacion de uno a muchos con la tabla products
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //Relacion de uno a uno con image
    public function image() {
        return $this->belongsTo(Image::class);
    }
}
