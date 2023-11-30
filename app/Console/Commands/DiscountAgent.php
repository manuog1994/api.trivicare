<?php

namespace App\Console\Commands;

use App\Models\Discount;
use App\Models\EventNot;
use Illuminate\Console\Command;

class DiscountAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discount:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete discount agent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //obtenemos todos los descuentos que tengan una fecha de inicio menor o igual a la actual
        $discounts = Discount::where('start_date', '>=', date('Y-m-d'))->get();

        //recorremos los descuentos
        foreach ($discounts as $discount) {
            //actualizamos el descuento
            $discount->update([
                'is_active' => false
            ]);

            $event = EventNot::create([
                'title' => 'Descuento desactivado',
                'description' => 'Descuento desactivado por fecha de inicio',
            ]);

            event(new \App\Events\MyEvent($event));
        }
        
        //obtenemos todos los descuentos que tengan una fecha de inicio mayor o igual a la actual
        $discounts = Discount::where('start_date', '<=', date('Y-m-d'))->get();

        //recorremos los descuentos
        foreach ($discounts as $discount) {
            //actualizamos el descuento
            $discount->update([
                'is_active' => true
            ]);

            $event = EventNot::create([
                'title' => 'Descuento activado',
                'description' => 'Descuento activado por fecha de inicio',
            ]);

            event(new \App\Events\MyEvent($event));
        }

        //obtenemos todos los descuentos que tengan una fecha de finalización mayor o igual a la actual
        $discounts = Discount::where('end_date', '<=', date('Y-m-d'))->get();

        
        //recorremos los descuentos
        foreach ($discounts as $discount) {
            //eliminamos el descuento
            $discount->delete();

            $event = EventNot::create([
                'title' => 'Descuento eliminado',
                'description' => 'Descuento eliminado por fecha de finalización',
            ]);

            event(new \App\Events\MyEvent($event));
        }
    }
}
