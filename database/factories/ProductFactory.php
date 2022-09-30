<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Crema Antiedad', 'Limpiador Facial', 'Serum cuello y escote', 'Neceser', 'Serum', 'Crema Corporal Cannabis', 'Crema hidratante', 'Bolsa ECO']),
            'description' => $this->faker->text,
            'specifications' => $this->faker->text,            
            'price' => $this->faker->randomFloat(2, 1, 100),
            'stock' => $this->faker->randomNumber(2),
            'barcode' => $this->faker->ean13,
            'category_id' => Category::all()->random()->id,
            'slug' => $this->faker->slug,
            'sold' => $this->faker->randomNumber(2),
            'status' => $this->faker->randomElement([Product::BORRADOR, Product::PUBLICADO]),
            'discount' => $this->faker->randomFloat(2, 1, 25),
            'weight' => $this->faker->randomFloat(2, 1, 100),
            'size' => $this->faker->randomNumber(3, 1, 100),
            'dimensions' => '20x15x10',
            
        ];
        
    }
}
