<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Category;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, ApiTrait;

    const BORRADOR = 1;
    const PUBLICADO = 2;

    protected $fillable = [
        'name',
        'description',
        'specifications',
        'price',
        'stock',
        'barcode',
        'category_id',
        'slug',
        'sold',
        'status',
        'review',
        'discount',
        'weight',
    ];
    protected $allowIncluded = ['category', 'category.products'];
    // Propiedad para filtrar por los campos de la tabla
    protected $allowFilter = ['id', 'name', 'slug', 'price', 'category_id'];
    // Propiedad para ordenar por los campos de la tabla
    protected $allowSort = ['id', 'name', 'price'];

    // Relacion de uno a uno con la tabla categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


}
