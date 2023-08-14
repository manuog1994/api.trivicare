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
    const RECHAZADO = 4;
    const CONTRAREEMBOLSO = 5;
    const TRANSFERENCIA = 6;


    // ESTADO DEL PEDIDO
    const RECIBIDO = 1;
    const PREPARANDO = 2;
    const ENVIADO = 3;
    const ENTREGADO = 4;
    const CANCELADO = 5;

    protected $fillable = [
        'id',
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
        'user_id',
        'user_profile_id',
        'guest_id',
        'products',
        'subTotal',
        'total',
        'coupon',
        'order_date',
        'paid',
        'status',
        'shipping',
        'token_id',
        'shipping_method',
        'invoice_paper',
        'note',
        'payment_method',
        'track',
        'token_reserve',
        'manual_order',
        'pickup_point',
        'email_sent',
        'confirmation_sent'
    ];

    protected $allowSort = ['id'];
    protected $allowStatus = ['status'];
    protected $allowFilter = ['status', 'user_profile_id'];
    protected $allowHistory = ['status', 'user_id', 'user_profile_id'];

    // Relacion uno a muchos inversa con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion uno a muchos inversa con UserProfile
    public function user_profile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    // Relacion uno a muchos inversa con UserProfile
    public function user_profiles()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Relacion uno a muchos inversa con Invoice
    public function invoice()
    {
        return $this->hasOne(InvoiceOrder::class);
    }
}
