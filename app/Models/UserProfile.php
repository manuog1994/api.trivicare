<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;
class UserProfile extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'user_id',
        'name',
        'lastname',
        'address',
        'optional_address',
        'number',
        'city',
        'state',
        'country',
        'zipcode',
        'gender',
        'birth_date',
        'phone',
        'dni',
    ];

    // Relacion uno a uno con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion uno a muchos con Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relacion uno a muchos con Invoice
    public function invoices()
    {
        return $this->hasMany(InvoiceOrder::class);
    }
}
