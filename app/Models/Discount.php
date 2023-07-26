<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class Discount extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'product_id',
        'discount',
        'start_date',
        'end_date',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
