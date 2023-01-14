<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class Review extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'user_id',
        'user_profile_id',
        'product_id',
        'message',
        'rating',
        'user_name',
        'user_lastname',
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
