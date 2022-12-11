<?php

namespace App\Http\Controllers\Api\Invoices;

use App\Models\InvoiceOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class InvoiceOrderController extends Controller
{
    public function index()
    {
        $invoices = InvoiceOrder::all();

        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
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
}
