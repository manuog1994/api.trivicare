<?php

namespace App\Http\Controllers\Api\Pdf;

use App\Models\InvoiceOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:admin');
       //$this->middleware('can:create')->only('show'); 
    }

    public function show($id)
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

}
