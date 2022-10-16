<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use HasFactory;

    const DESACTIVADO = 1;
    const ACTIVADO = 2;

    protected $fillable = [
        'code',
        'discount',
        'validity',
        'status',
    ];
}
