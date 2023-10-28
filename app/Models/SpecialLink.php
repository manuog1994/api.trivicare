<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'param',
        'discount',
        'max_uses',
        'is_active',
    ];
}
