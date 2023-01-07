<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Reserve;
use Illuminate\Console\Command;

class ReserveAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reserve:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete reserves that have expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reserves = Reserve::all();

        foreach ($reserves as $reserve) {
            $json = json_decode($reserve->products);
    
            if($reserve->created_at < now()->subMinutes(15)) {
                foreach ($json as $item) {
                    $product = Product::where('id', $item->id)->first();
                    $product->stock = $product->stock + $item->cartQuantity;
                    $product->save();
                }
        
                $reserve->delete();
            }
        }
    }
}
