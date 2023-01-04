<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lastname',
        'address',
        'optional_address',
        'city',
        'state',
        'zipcode',
        'country',
        'email',
        'phone',
        'dni',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
