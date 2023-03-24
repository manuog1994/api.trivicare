<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserProfile;
use App\Mail\CancelOrderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CancelOrderAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel order in status "pending" after 48 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // DEVUELVE TODOS LOS PEDIDOS QUE NO ESTEN EN PAID 3 Y QUE NO ESTEN EN STATUS 5 Y QUE HAYAN PASADO MAS DE 48 HORAS DESDE SU CREACION
        //
        //
        $orders = Order::where('paid', '!=', 'PAGADO')->where('status', '!=', 5)->where('created_at', '<', now()->subHours(48))->get();


        // RECORREMOS TODOS LOS PEDIDOS Y LOS CANCELAMOS

        foreach ($orders as $order) {
            //COMPROBAMOS SI EL PEDIDO ES DE UN USUARIO REGISTRADO O NO
            if($order->guest_id == null){
                $user = User::where('id', $order->user_id)->first();
                $user_profile = UserProfile::where('id', $order->user_profile_id)->first();
            }else{
                $user = Guest::where('id', $order->guest_id)->first();
                $user_profile = Guest::where('id', $order->guest_id)->first();
            }
            //DECODIFICAMOS LOS PRODUCTOS DEL PEDIDO
            $products = json_decode($order->products);

            //RECORREMOS LOS PRODUCTOS Y DEVOLVEMOS EL STOCK
            foreach($products as $item){
                $product = Product::where('id', $item->id)->first();
                $product->stock = $product->stock + $item->cartQuantity;
                $product->save();
            }

            //ENVIAMOS EL CORREO DE CANCELACION
            $mailData = [
                'title' => 'Confirmación de pedido',
                'body' => 'Gracias por tu pedido. Te adjuntamos la factura de tu pedido.',

            ];
             
            Mail::to($user->email)->send(new CancelOrderMail($mailData));
            
            //CAMBIAMOS EL STATUS DEL PEDIDO A CANCELADO Y LO GUARDAMOS
            $order->status = 5;
            $order->save();
        }
    }
}
