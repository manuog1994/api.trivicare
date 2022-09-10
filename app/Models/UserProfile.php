<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'optional_address',
        'number',
        'city',
        'state',
        'country',
        'zipcode',
        'gender',
        'birth_date',
        'phone'
    ];

    // Relacion uno a uno con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
