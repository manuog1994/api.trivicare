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
        // DEVUELVE TODAS LAS RESERVAS QUE NO SE HAN COMPRADO Y QUE EL PAYMENT_METHOD NO SEA TRANSFER_BANK O BIZUM
        $reserves = Reserve::where('paid', '!=', 'PAGADO')->where('payment_method', '!=', 'transfer_bank')->where('payment_method', '!=', 'bizum')->where('created_at', '<', now()->subMinutes(15))->get();

        // RECORREMOS TODAS LAS RESERVAS, DEVOLVEMOS EL STOCK Y LAS CANCELAMOS
        foreach ($reserves as $reserve) {
            $json = json_decode($reserve->products);
    
            foreach ($json as $item) {
                $product = Product::where('id', $item->id)->first();
                $product->stock = $product->stock + $item->cartQuantity;
                $product->save();
            }
    
            $reserve->delete();

        }
    }
}
