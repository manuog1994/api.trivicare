<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'product_id',
     ];

    // Relación uno a muchos con la tabla products inversa
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }
}
