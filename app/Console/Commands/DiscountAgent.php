<?php

namespace App\Console\Commands;

use App\Models\Discount;
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
        //obtenemos todos los descuentos que tengan una fecha de finalización mayor o igual a la actual
        $discounts = Discount::where('end_date', '<=', date('Y-m-d'))->get();

        
        //recorremos los descuentos
        foreach ($discounts as $discount) {
            //eliminamos el descuento
            $discount->delete();
        }
    }
}
