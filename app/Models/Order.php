<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class Order extends Model
{
    use HasFactory, ApiTrait;

    // ESTADO DEL PAGO
    const PENDIENTE = 1;
    const PROCESANDO = 2;
    const PAGADO = 3;

    // ESTADO DEL PEDIDO
    const RECIBIDO = 1;
    const PREPARANDO = 2;
    const ENVIADO = 3;
    const ENTREGADO = 4;

    protected $fillable = [
        'number_bill',
        'user_id',
        'user_profile_id',
        'products',
        'total',
        'coupon',
        'order_date',
        'paid',
        'status',
    ];

    protected $allowSort = ['id'];
    protected $allowStatus = ['status'];
    protected $allowFilter = ['status'];

    // Relacion uno a muchos inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
