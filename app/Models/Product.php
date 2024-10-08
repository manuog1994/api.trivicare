<?php

namespace App\Models;

use App\Models\Category;

use App\Traits\ApiTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, ApiTrait;

    const BORRADOR = 1;
    const PUBLICADO = 2;
    const NUEVO = 1;
    const VIEJO = 2;

    protected $fillable = [
        'id',
        'name',
        'description',
        'specifications',
        'price',
        'price_base',
        'stock',
        'barcode',
        'category_id',
        'slug',
        'sold',
        'status',
        'weight',
        'size',
        'dimensions',
        'rating',
        'total_reviews',
        'price_discount',
        'new',
        'meta_description',
        'best_seller',
        'ingredients',
     ];
     // Propiedad para filtrar por los campos de la tabla
    protected $allowFilter = ['id', 'name', 'slug', 'price', 'category_id', 'tags', 'status'];
    // Propiedad para ordenar por los campos de la tabla
    protected $allowSort = ['id', 'name', 'price_discount'];

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
        return $this->belongsToMany(Tag::class)->withPivot('product_id', 'tag_id', 'name', 'slug');
    }

    // Relacion de uno a muchos con la tabla images
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    //Relacion de uno a muchos con la table variations
    public function variations() 
    {
        return $this->hasMany(Variation::class);
    }

    // Relacion de uno a uno con la tabla discount
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }
}
