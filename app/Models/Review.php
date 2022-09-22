<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'message',
        'rating',
    ];

    // Relacion de uno a muchos con la tabla products
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relacion de uno a uno con la tabla users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
