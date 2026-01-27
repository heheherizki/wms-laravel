<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    // 1. LIST INVOICE (Halaman Dashboard Invoice)
    public function index(Request $request)
    {
        $query = Invoice::with(['salesOrder.customer', 'shipment']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    // 2. GENERATE INVOICE DARI SHIPMENT (Action)
    public function createFromShipment($shipmentId)
    {
        $shipment = Shipment::with(['details', 'salesOrder.details'])->findOrFail($shipmentId);

        // Cek apakah shipment ini sudah punya invoice?
        if ($shipment->invoice) {
            return back()->with('error', 'Shipment ini sudah ditagihkan (Invoice sudah ada).');
        }

        DB::transaction(function () use ($shipment) {
            // Generate No Invoice: INV/202601/0001
            $lastInv = Invoice::latest()->first();
            $nextId = $lastInv ? $lastInv->id + 1 : 1;
            $invNumber = 'INV/' . date('Ym') . '/' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Buat Header Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invNumber,
                'shipment_id'    => $shipment->id,
                'sales_order_id' => $shipment->sales_order_id,
                'date'           => date('Y-m-d'),
                'due_date'       => date('Y-m-d', strtotime('+30 days')), // Default jatuh tempo 30 hari
                'total_amount'   => 0, // Hitung di bawah
                'status'         => 'unpaid'
            ]);

            $total = 0;

            // Buat Detail (Ambil Qty dari Shipment, Ambil Harga dari SO)
            foreach ($shipment->details as $shipItem) {
                // Cari harga jual item ini di Sales Order (Agar konsisten dengan kesepakatan awal)
                $soDetail = $shipment->salesOrder->details->where('product_id', $shipItem->product_id)->first();
                $price = $soDetail ? $soDetail->price : 0;

                $subtotal = $shipItem->quantity * $price;
                $total += $subtotal;

                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $shipItem->product_id,
                    'quantity'   => $shipItem->quantity,
                    'price'      => $price,
                    'subtotal'   => $subtotal
                ]);
            }

            // Update Total
            $invoice->update(['total_amount' => $total]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diterbitkan!');
    }

    // 3. CETAK PDF
    public function print($id)
    {
        $invoice = Invoice::with(['salesOrder.customer', 'details.product'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.print', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');
        
        // --- PERBAIKAN DI SINI ---
        // Ganti tanda '/' dengan '-' agar nama file valid
        $filename = 'INV-' . str_replace('/', '-', $invoice->invoice_number) . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function show($id)
    {
        // Load invoice beserta relasi yang dibutuhkan di view
        $invoice = Invoice::with(['salesOrder.customer', 'salesOrder.user', 'details.product', 'payments.user'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }
}