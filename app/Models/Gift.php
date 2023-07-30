<?php

namespace App\Models;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'name',
        'for_total', // 'for_total' is added to the fillable array
        'stock',
        'image_path'
    ];
}
