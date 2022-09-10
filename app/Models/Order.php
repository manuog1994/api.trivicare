<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'product_collection',
        'total',
        'count',
        'order_date',
        'status',
    ];

    // Relacion uno a muchos inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
