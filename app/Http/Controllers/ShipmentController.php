<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Invoice; // Import Invoice
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ShipmentController extends Controller
{
    // 1. LIST PENGIRIMAN & MODAL PILIH SO
    public function index(Request $request)
    {
        $query = Shipment::with(['salesOrder.customer', 'invoice', 'user']);

        // 1. Filter Pencarian Global (No SJ, No SO, Nama Customer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                ->orWhereHas('salesOrder', function($so) use ($search) {
                    $so->where('so_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // 3. Filter Status Invoice
        if ($request->filled('status')) {
            if ($request->status == 'invoiced') {
                $query->has('invoice');
            } elseif ($request->status == 'uninvoiced') {
                $query->doesntHave('invoice');
            }
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();

        // Data untuk Modal "Buat Shipment" (SO yang siap dikirim)
        $readySalesOrders = SalesOrder::with('customer')
            ->whereIn('status', ['pending', 'partial'])
            ->where('status', '!=', 'on_hold') // Jangan munculkan yang di-hold
            ->latest()
            ->get();

        return view('shipments.index', compact('shipments', 'readySalesOrders'));
    }

    // 2. FORM PENGIRIMAN (Logic Sama, Cuma View Sedikit Beda nanti)
    public function create($salesOrderId)
    {
        $so = SalesOrder::with(['details.product', 'customer'])->findOrFail($salesOrderId);

        // Validasi Status
        if ($so->status == 'shipped') return back()->with('error', 'Order ini sudah selesai dikirim sepenuhnya.');
        if ($so->status == 'on_hold') return back()->with('error', 'Order ini sedang ditahan (On Hold). Hubungi Admin/Finance.');

        // Generate No SJ
        $lastShipment = Shipment::latest()->first();
        $nextId = $lastShipment ? $lastShipment->id + 1 : 1;
        $sjNumber = 'SJ-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('shipments.create', compact('so', 'sjNumber'));
    }

    // 3. PROSES SIMPAN (UPDATE: Auto Invoice & Redirect ke Index)
    public function store(Request $request, $salesOrderId)
    {
        $so = SalesOrder::with('details')->findOrFail($salesOrderId);

        $request->validate([
            'date' => 'required|date',
            'shipment_number' => 'required|unique:shipments,shipment_number',
            'items' => 'required|array',
        ]);

        $newShipmentId = null;
        $newInvoiceId = null;

        DB::transaction(function () use ($request, $so, &$newShipmentId, &$newInvoiceId) {
            
            // A. Buat Header Shipment
            $shipment = Shipment::create([
                'shipment_number' => $request->shipment_number,
                'sales_order_id' => $so->id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
            ]);
            $newShipmentId = $shipment->id;

            $totalShippedInThisSession = 0;

            // B. Loop Barang
            foreach ($request->items as $productId => $qtyToShip) {
                $qtyToShip = (int) $qtyToShip;
                if ($qtyToShip <= 0) continue;

                $soDetail = $so->details->where('product_id', $productId)->first();
                $product = Product::findOrFail($productId);

                if (!$soDetail) throw new \Exception("Produk ID {$productId} tidak valid.");

                // Validasi Jumlah
                $remainingQty = $soDetail->quantity - $soDetail->shipped_quantity;
                if ($qtyToShip > $remainingQty) {
                    throw new \Exception("Produk {$product->name}: Melebihi sisa pesanan.");
                }
                if ($product->stock < $qtyToShip) {
                    throw new \Exception("Stok gudang tidak cukup untuk {$product->name}.");
                }

                // Update Stok & SO
                $product->decrement('stock', $qtyToShip);
                $soDetail->increment('shipped_quantity', $qtyToShip);

                ShipmentDetail::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $productId,
                    'quantity' => $qtyToShip,
                ]);

                // Log Kartu Stok
                Transaction::create([
                    'product_id' => $productId,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $qtyToShip,
                    'reference' => $shipment->shipment_number,
                    'notes' => 'Pengiriman SO: ' . $so->so_number,
                ]);

                $totalShippedInThisSession++;
            }

            if ($totalShippedInThisSession == 0) {
                throw new \Exception("Isi jumlah kirim minimal satu barang.");
            }

            // C. Update Status SO
            $so->refresh();
            $allCompleted = true;
            foreach ($so->details as $detail) {
                if ($detail->shipped_quantity < $detail->quantity) {
                    $allCompleted = false; break;
                }
            }
            $so->update(['status' => $allCompleted ? 'shipped' : 'partial']);

            // D. [FITUR BARU] AUTO GENERATE INVOICE
            if ($request->has('create_invoice')) {
                // Logic Invoice Sederhana (Copy dari Shipment)
                // Generate Invoice Number
                $lastInv = Invoice::latest()->first();
                $nextInvId = $lastInv ? $lastInv->id + 1 : 1;
                $invNumber = 'INV-' . date('Ym') . '-' . str_pad($nextInvId, 4, '0', STR_PAD_LEFT);

                // Hitung Nilai Invoice (Hanya barang yang dikirim sekarang)
                $invoiceTotal = 0;
                foreach ($shipment->details as $detail) {
                    // Ambil harga dari SO Detail
                    $price = $so->details->where('product_id', $detail->product_id)->first()->price;
                    $invoiceTotal += ($price * $detail->quantity);
                }

                $invoice = Invoice::create([
                    'invoice_number' => $invNumber,
                    'sales_order_id' => $so->id,
                    'shipment_id' => $shipment->id,
                    'due_date' => now()->addDays($so->customer->payment_terms ?? 0), // Sesuai termin
                    'total_amount' => $invoiceTotal,
                    'status' => 'unpaid',
                ]);
                
                $newInvoiceId = $invoice->id;
            }
        });

        // Redirect ke Index dengan Session Data untuk tombol Print
        return redirect()->route('shipments.index')->with([
            'success' => 'Pengiriman berhasil diproses!',
            'new_shipment_id' => $newShipmentId,
            'new_invoice_id' => $newInvoiceId
        ]);
    }

    // 4. PRINT (Tidak berubah)
    public function print($id)
    {
        $shipment = Shipment::with(['salesOrder.customer', 'details.product', 'user'])->findOrFail($id);
        $pdf = Pdf::loadView('shipments.print', compact('shipment'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('SJ-' . $shipment->shipment_number . '.pdf');
    }

    // 5. LIHAT DETAIL PENGIRIMAN
    public function show($id)
    {
        $shipment = Shipment::with([
            'salesOrder.customer', 
            'details.product', 
            'user',
            'invoice'
        ])->findOrFail($id);

        return view('shipments.show', compact('shipment'));
    }
}