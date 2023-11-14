<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Cupon;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserProfile;
use App\Models\InvoiceOrder;
use Illuminate\Console\Command;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class GenerateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:invoice {number}';

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
        $number = $this->argument('number');

        // enviar email de confirmacion de la ordenes que no han sido enviadas y que hayan sido creadas hace un máximo de 10 minutos
        $order = Order::find($number);

        // Si la orden no existe
        if ($order == null) {
            $this->error('La orden no existe');
            return;
        }

        // Si la orden ya tiene una factura
        if ($order->invoice != null) {
            $qOne = $this->ask('La orden ya tiene una factura. Desea continuar S/N?');

            if ($qOne == 'N' || $qOne == 'n') {
                return;
            }
        }

        // Si la orden no está pagada
        if ($order->paid != 'PAGADO') {
            $qTwo = $this->ask('La orden no está pagada. Desea continuar S/N?');

            if ($qTwo == 'N' || $qTwo == 'n') {
                return;
            }

            $qThree = $this->ask('Desea cambiar el estado de la orden a PAGADO? S/N');

            if ($qThree == 'S' || $qThree == 's') {
                $order->paid = 'PAGADO';
                $order->save();
            }

        }

        $this->info('Generando factura...');

        // decode the products
        $products = json_decode($order->products);

        // get the products from the database
        foreach ($products as $product) {
            $product = Product::where('id', $product->id)->first();
        } 



        // fill the data for the invoice
        $client = new Party([
            'name' => 'Trivicare Natural Cosmetics',
            'custom_fields' => [
                'Nombre' => 'Cristina Triviño Cortés',
                'DNI' => '45923103S',
                'email' => 'info@trivicare.com',
                'teléfono' => '613036942',
            ],
        ]);

        $customer = new Party([
            'name'          => $order->name . ' ' . $order->lastname,
            'address'       => $order->address,
            'postal_code'   => $order->zipcode,
            'city'          => $order->city,
            'state'         => $order->state,
            'country'       => $order->country,
            'custom_fields' => [
                'DNI' => $order->dni,
                'email'=> $order->email,
                'teléfono' => $order->phone,
            ],
        ]);

        foreach($products as $item) {
            if(isset($item->variation)) {
                $item->name = $item->name . ' -- ' . $item->variation;
            }

            // if(isset($item->discount)) {
            //     foreach($item->discount as $discount) {
            //         $item->discount = $discount->discount;
            //     }
            // }
            //comprobar si el descuento es un entero o un float
            $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent( $item->discount === null ? 0 : $item->discount->discount)->taxByPercent(21);
        }

            
        if($order->coupon == null) {
            $discnt = null; 
        } else {
            $coupon = Cupon::where('code', $order->coupon)->first();
            $discnt = $coupon->discount;
        }
        //find number for the invoice
        $inv = InvoiceOrder::where('order_id', $order->id)->first();
        if ($inv == null) {
            //make a generator number for the invoice
            $invt = InvoiceOrder::all();
            $year = Carbon::now()->format('y');

            if($invt->count() == 0){

                $invoice_number = '#TNC'. $year . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
    
            }else {
    
                $last = substr($invt->last()->invoice_number, 0, -6);
                $headerInv = '#TNC' . $year;
    
                if($last != $headerInv) {
                    $invoice_number = '#TNC'. $year . '/' . str_pad(1, 5, '0', STR_PAD_LEFT);
                } else {
                    $last_invoice = $invt->last();
                    $invoice_number = str_replace('#TNC' . $year . '/', '', $last_invoice->invoice_number);
                    $invoice_number += 1;
                    $invoice_number = '#TNC'. $year . '/' . str_pad($invoice_number, 5, '0', STR_PAD_LEFT);
                } 
    
            }
        } else {
            $invoice_number = $inv->invoice_number;
        }


        $dateInv= date('dmY');

        $invoice = Invoice::make('receipt')
            //->series('#TNC'. strval($year)) 
            //->sequence(number_format(substr($invoice_number, -5)))
            ->serialNumberFormat($invoice_number)
            ->status(__('invoices::invoice.paid'))
            ->seller($client)
            ->buyer($customer)
            ->date(now())
            ->dateFormat('d/m/Y')
            ->payUntilDays(14)
            ->currencySymbol('€')
            ->currencyCode('EUR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($dateInv . '_' . substr($invoice_number, 7, 5))
            ->addItems($items)
            ->setCustomData($discnt)
            ->taxableAmount($order->subTotal)
            ->totalAmount($order->total)
            ->shipping($order->shipping)
            ->logo(public_path('img/logofactura.png'))
            // You can additionally save generated invoice to configured disk
            ->save('public');
        
            $link = $invoice->url();
            // eliminar los '/' y '-'del nombre del archivo
            $filename = $invoice->filename;
    
            // Update dates in the database
            InvoiceOrder::updateOrCreate(['order_id' => $order->id],[
                'user_profile_id' => $order->user_profile->id ?? null,
                'filename' => $filename,
                'url' => $link,
                'invoice_number' => $invoice_number,
                'name' => $order->name,
                'lastname' => $order->lastname,
                'email' => $order->email,
                'address' => $order->address,
                'city' => $order->city,
                'zipcode' => $order->zipcode,
                'state' => $order->state,
                'country' => $order->country,
                'phone' => $order->phone,
                'dni' => $order->dni,
                'total' => ($order->total * 1.21) + $order->shipping,
                'type' => 'Particular',
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
    
            $this->info('La factura se ha generado correctamente');
            $this->info('El link de la factura es: ' . $link);
    }

    
}
