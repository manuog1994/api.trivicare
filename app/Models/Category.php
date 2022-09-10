<?php

namespace App\Models;

use App\Models\Product;
use App\Traits\ApiTrait;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, ApiTrait;

    protected $fillable = [
        'name',
        'slug',
    ];
    protected $allowIncluded = ['products', 'products.category'];
    // Propiedad para filtrar por los campos de la tabla
    protected $allowFilter = ['id', 'name', 'slug'];
    // Propiedad para ordenar por los campos de la tabla
    protected $allowSort = ['id', 'name', 'slug'];

    // Relacion de uno a muchos con la tabla products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
