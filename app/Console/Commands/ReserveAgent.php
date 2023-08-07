<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Reserve;
use App\Models\EventNot;
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
        // DEVUELVE TODAS LAS RESERVAS QUE NO SE HAN COMPRADO EN 30 MINUTOS
        $reserves = Reserve::where('created_at', '<', now()->subMinutes(30))->get();

        // RECORREMOS TODAS LAS RESERVAS, DEVOLVEMOS EL STOCK Y LAS CANCELAMOS
        foreach ($reserves as $reserve) {
            $json = json_decode($reserve->products);
    
            foreach ($json as $item) {
                $product = Product::where('id', $item->id)->first();
                $product->stock = $product->stock + $item->cartQuantity;
                $product->save();
            }
    
            $reserve->delete();

            $event = EventNot::create([
                'title' => 'Reserva cancelada',
                'description' => 'Reserva cancelada por inactividad',
            ]);
            
            event(new \App\Events\MyEvent($event));
        }
    }
}
