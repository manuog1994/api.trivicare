<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\EventNot;
use Illuminate\Console\Command;

class StockListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:listen';

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
       $products = Product::where('stock', '<=', 1)->get();

       if (count($products) > 0) {
        //mandamos un evento
        $event = EventNot::create([
            'title' => 'Stock bajo',
            'description' => 'Hay productos con stock bajo',
        ]);

        event(new \App\Events\MyEvent($event));
       }
    }
}
