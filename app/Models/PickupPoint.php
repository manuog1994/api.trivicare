<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiTrait;

class PickupPoint extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'name',
        'address',
        'state_id',
        'phone',
        'hours',
    ];

    protected $allowFilter = ['id'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
