<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class UpdateNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:new_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update new status to old products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::all();
        foreach($products as $product) {
            // Si el producto tiene más de un mes de antigüedad, se marca como antiguo.
            if($product->created_at < now()->subMonth()) {
                $product->new = 2;
                $product->save();
            }
        }
    }
}
