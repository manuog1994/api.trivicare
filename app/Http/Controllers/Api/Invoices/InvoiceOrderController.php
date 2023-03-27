<?php

namespace App\Http\Controllers\Api\Invoices;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Mail\ManualOrderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoiceOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:create')->only('newInvoice');
        $this->middleware('can:edit')->only('index');
        $this->middleware('can:delete')->only('destroy');
    }

    public function index()
    {
        // Cargar todas las facturas de la base de datos, ordenadas por fecha de creación descendente, cargando la relación de ordenes y la relación que tiene ordenes con perfiles de usuario
        $invoices = InvoiceOrder::with(['order', 'order.user_profile', 'order.guest'])->orderBy('created_at', 'desc')->getOrPaginate();



        return InvoiceResource::collection($invoices);
    }

    public function show(InvoiceOrder $invoiceOrder)
    {
        return response()->json([
            'success' => true,
            'data' => $invoiceOrder
        ]);
    }

    public function downloadFile($id)
    {
        $invoiceOrder = InvoiceOrder::where('id', $id)->first();
        $filename = $invoiceOrder->filename;
        $path = Storage::disk('public')->exists($filename);

        if ($path) {
            return Storage::disk('public')->download($filename);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $invoiceOrder
        ]);
    }

    // Descarga multiples archivos en un zip
    public function multipleDownloads(Request $request)
    {
        $json = json_decode($request->selecteds);

        $files = [];

        foreach ($json as $id) {
            $invoiceOrder = InvoiceOrder::where('id', $id)->first();
            $filename = $invoiceOrder->filename;
            $path = Storage::disk('public')->exists($filename);

            if ($path) {
                // Guarda el path de los archivos en un array
                $files[] = Storage::disk('public')->path($filename);
            }
        }


        $zip = new \ZipArchive();
        $zipName = 'invoices.zip';
        $zip->open($zipName, \ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFile($file);
        }
        $zip->close();

        return response()->download($zipName);
    }

    public function newInvoice(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zipcode' => 'required',
            'dni' => 'required',
            'products' => 'required',
            'subTotal' => 'required',
            'total' => 'required',
        ]);

        $order = Order::create([
            'user_id' => 2,
            'user_profile_id' => 1,
            'products' => $request->products,
            'subTotal' => $request->subTotal,
            'total' => $request->total,
            'order_date' => Carbon::now()->format('d-m-Y' . ' ' . 'H:i'),
            'paid' => Order::PAGADO,
            'status' => Order::ENTREGADO,
            'shipping' => 0,
            'shipping_method' => 'En mano',
            'payment_method' => 'cash',
            'manual_order' => '1',
            'email_sent' => true,
            'confirmation_sent' => true,
        ]);

        

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
            'name'          =>  $request->name . ' ' . $request->lastname,
            'address'       => $request->address,
            'postal_code'   => $request->zipcode,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'custom_fields' => [
                'DNI' => $request->dni,
                'email' => $request->email,
                'teléfono' => $request->phone,
            ],
        ]);

        $products = json_decode($request->products);
        
        foreach($products as $item) {
            $items[] = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price_base)->quantity($item->cartQuantity)->discountByPercent($item->discount)->taxByPercent(21);
        }


        $discnt = $request->discount;
        
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

        $dateInv= Carbon::now()->format('d/m/Y');

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
        // Eliminar los caracteres '/' y '-' del nombre del archivo
        $filename = str_replace('/', '', $invoice->filename);

        // Then send email to party with link
        $inv = InvoiceOrder::create([
            'user_profile_id' => $order->user_profile->id,
            'order_id' => $order->id,
            'filename' => $filename,
            'url' => $link,
            'invoice_number' => $invoice_number,
        ]);
        $mailDiscount = $request->total * $request->discount / 100;
        $mailData = [
            'title' => 'Factura de tu compra',
            'body' => 'Gracias por tu compra. Te adjuntamos la factura de tu compra.',
            'date' => $order->order_date,
            'order' => $invoice_number,
            'user' => $request->name . ' ' . $request->lastname,
            'address' => $request->address,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'state' => $request->state,
            'country' => $request->country,
            'email' => $request->email,
            'products' => $products,
            'discount' => $mailDiscount,
            'subTotal' => round($request->total * 1.21, 2),
            'total' => round(($request->total - $mailDiscount) * 1.21, 2),
            'invoice' => $filename
        ];
         
        Mail::to($request->email)->send(new ManualOrderMail($mailData));

        return response()->json([
            'message' => 'Factura creada correctamente',
            'data' => $inv->url
        ]);
    }
}
