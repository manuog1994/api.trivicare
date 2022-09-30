<?php

namespace App\Models;

use App\Models\Category;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\Builder;
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
        'size',
        'dimensions',
        'rating',
        'total_reviews',
        'price_discount',
     ];
     // Propiedad para filtrar por los campos de la tabla
    protected $allowFilter = ['id', 'name', 'slug', 'price', 'category_id', 'tags'];
    // Propiedad para ordenar por los campos de la tabla
    protected $allowSort = ['id', 'name', 'price_discount'];

    protected $allowTags = ['id', 'name', 'tag_id'];


    // Relacion de uno a uno con la tabla categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relacion de uno a muchos con la tabla reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relacion de muchos a muchos con la tabla tags con la tabla pivote product_tag
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

}
