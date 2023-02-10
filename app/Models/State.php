<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'state',
    ];

    public function pickupPoints()
    {
        return $this->hasMany(PickupPoint::class);
    }
}
