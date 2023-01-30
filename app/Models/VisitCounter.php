<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'page_visited',
    ];
}
