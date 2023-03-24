<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class DeleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete orders in paid PENDING after 72 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // DEVUELVE TODOS LOS PEDIDOS QUE NO ESTEN EN PAID PENDIENTE O PROCESANDO Y QUE HAYAN PASADO MAS DE 72 HORAS DESDE SU CREACION
        $orders = Order::where('paid', 'PENDIENTE')->orWhere('paid', 'PROCESANDO')->where('created_at', '<', now()->subHours(72))->get();

        // RECORREMOS TODOS LOS PEDIDOS Y LOS CANCELAMOS
        foreach ($orders as $order) {
            $order->delete();
        }
    }
}
