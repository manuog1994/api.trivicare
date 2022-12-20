<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'filename',
        'order_id',
        'user_profile_id',
        'url',
        'invoice_number',

    ];
}
