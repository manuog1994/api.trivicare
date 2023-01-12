<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class OrderAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all orders that are in paid PROCESANDO older than 1 day.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Order::where('paid', 'PROCESANDO')->where('created_at', '<', now()->subDay())->get();
        foreach ($orders as $order) {
            $order->delete();
        }
        $orders = Order::where('paid', 'PENDIENTE')->where('created_at', '<', now()->subDay())->get();
        foreach ($orders as $order) {
            $order->delete();
        }
    }
}
