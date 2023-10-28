<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateSpecialLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'special:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $product = $this->ask('Cuál es el producto?
        1. CBD
        2. Contorno de ojos
        3. Cuello y escote
        4. Crema Hidratante
        5. Serum
        6. Leche limpiadora
        7. Totte Bag Girasol
        8. Totte Bag Mujer
        9. Totte Bag Pride
        10. Neceser
        11. Pack Básico
        12. Pack Imprescindibles
        13. Pack Completo
        ');
        $discount = $this->ask('Cuál es el descuento?');
        $max_uses = $this->ask('Cuál es el máximo de usos?');
        $is_active = $this->ask('Está activo? (s para sí, cualquier tecla para no)');


        // Localizar product del producto a partir del número
        if ($product == 1) {
            $product = 6;
        } elseif ($product == 2) {
            $product = 2;
        } elseif ($product == 3) {
            $product = 1;
        } elseif ($product == 4) {
            $product = 5;
        } elseif ($product == 5) {
            $product = 4;
        } elseif ($product == 6) {
            $product = 3;
        } elseif ($product == 7) {
            $product = 65;
        } elseif ($product == 8) {
            $product = 64;
        } elseif ($product == 9) {
            $product = 63;
        } elseif ($product == 10) {
            $product = 55;
        } elseif ($product == 11) {
            $product = 57;
        } elseif ($product == 12) {
            $product = 59;
        } elseif ($product == 13) {
            $product = 61;
        } 

        $product_id = \App\Models\Product::find($product)->id;

        $special_link = \App\Models\SpecialLink::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'product_id' => $product_id,
            'param' => 'exclusive=',
            'discount' => $discount,
            'max_uses' => $max_uses,
            'is_active' => $is_active == 's' ? true : false,
        ]);
        
    }
}
