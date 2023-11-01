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

        // decode the products
        $products = json_decode($order->products);

        // get the products from the database
        foreach ($products as $product) {
            $product = Product::where('id', $product->id)->first();
        } 

        // si el pago es por paypal o redsys y el estado es pagado
        if ($order->payment_method == 'paypal' && $order->paid == 'PAGADO' || $order->payment_method == 'redsys' && $order->paid == 'PAGADO' || $order->payment_method == 'transfer_bank' && $order->paid == 'PAGADO' || $order->payment_method == 'bizum' && $order->paid == 'PAGADO' || $order->payment_method == 'paylater' && $order->paid == 'PAGADO') {

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
            //si existe variacion en el producto
            if(isset($item->variation)) {
                $item->name = $item->name . ' -- ' . $item->variation;
            }

            $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent($item->discount)->taxByPercent(21);
        }

            
        if($order->coupon == null) {
            $discnt = null; 
        } else {
            $coupon = Cupon::where('code', $order->coupon)->first();
            $discnt = $coupon->discount;
        }
        //find number for the invoice
        $inv = InvoiceOrder::where('order_id', $order->id)->first();
        $invoice_number = $inv->invoice_number;


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
            
        }
    }

    
}
