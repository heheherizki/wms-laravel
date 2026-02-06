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
        // 1. Eager Loading Relasi
        $query = Invoice::with(['salesOrder.customer', 'shipment']);

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('salesOrder.customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('shipment', function($s) use ($search) {
                      $s->where('shipment_number', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Filter Status Pembayaran
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // 4. Filter Jatuh Tempo
        if ($request->filled('due_date_start')) {
            $query->whereDate('due_date', '>=', $request->due_date_start);
        }
        if ($request->filled('due_date_end')) {
            $query->whereDate('due_date', '<=', $request->due_date_end);
        }

        // 5. Eksekusi Data
        $invoices = $query->latest()->paginate(10)->withQueryString();

        // 6. Statistik Keuangan Ringkas (FIXED)
        // Kita hitung menggunakan operasi matematika di database: SUM(total - dibayar)
        $stats = [
            'unpaid_count' => Invoice::where('status', 'unpaid')->count(),
            
            'overdue_count' => Invoice::where('status', '!=', 'paid')
                ->whereDate('due_date', '<', now())
                ->count(),
            
            // PERBAIKAN: Hitung via PHP Collection agar tidak error SQL
            'total_receivable' => Invoice::whereIn('status', ['unpaid', 'partial'])
                ->get()
                ->sum('remaining_balance'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
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