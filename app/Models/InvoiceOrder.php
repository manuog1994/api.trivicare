<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class InvoiceOrder extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'id',
        'filename',
        'order_id',
        'user_profile_id',
        'url',
        'invoice_number',
        'order_seller_id',
        'seller_id',
        'name',
        'lastname',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zipcode',
        'country',
        'dni',
        'total',
        'type',
    ];

    // Relación uno a uno con Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
