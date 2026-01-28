<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Product;
use App\Models\Transaction; // Pastikan Model Transaction di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ShipmentController extends Controller
{
    // 1. LIST PENGIRIMAN
    public function index(Request $request)
    {
        $query = Shipment::with(['salesOrder.customer', 'user']);

        // Filter Tanggal
        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        $shipments = $query->latest()->paginate(10);

        return view('shipments.index', compact('shipments'));
    }

    // 2. FORM PENGIRIMAN (GATEKEEPER)
    public function create($salesOrderId)
    {
        $so = SalesOrder::with(['details.product', 'customer'])->findOrFail($salesOrderId);

        if ($so->status == 'shipped') {
            return back()->with('error', 'Order sudah dikirim.');
        }

        // === LOGIKA BARU: CEK IZIN CUSTOMER ===
        
        // Cek apakah CUSTOMER punya izin sakti? (Bukan SO nya lagi)
        $isAuthorized = $so->customer->authorized_until && now()->lt($so->customer->authorized_until);

        // Jika Customer TIDAK punya izin, baru cek kesehatan keuangannya
        if (!$isAuthorized) {
            
            // 1. Cek Overdue
            if ($so->customer->hasOverdueInvoices()) {
                $so->update(['status' => 'on_hold']); 
                return redirect()->route('sales.show', $so->id)
                    ->with('error', 'BLOKIR: Customer memiliki invoice overdue. Silakan Unlock Customer di menu Data Customer.');
            }

            // 2. Cek Limit
            if ($so->customer->credit_limit > 0 && $so->payment_status != 'paid') {
                $exposure = $so->customer->current_debt + $so->grand_total;
                if ($exposure > $so->customer->credit_limit) {
                    $so->update(['status' => 'on_hold']);
                    return redirect()->route('sales.show', $so->id)
                        ->with('error', 'BLOKIR: Over Credit Limit. Silakan Unlock Customer di menu Data Customer.');
                }
            }
        }
        // --- END GATEKEEPER ---

        // Generate No Surat Jalan: SJ-YYYYMM-XXXX
        $lastShipment = Shipment::latest()->first();
        $nextId = $lastShipment ? $lastShipment->id + 1 : 1;
        $sjNumber = 'SJ-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('shipments.create', compact('so', 'sjNumber'));
    }

    // 3. PROSES SIMPAN (POTONG STOK & UPDATE SO)
    public function store(Request $request, $salesOrderId)
    {
        $so = SalesOrder::with('details')->findOrFail($salesOrderId);

        $request->validate([
            'date' => 'required|date',
            'shipment_number' => 'required|unique:shipments,shipment_number',
            'items' => 'required|array', // Array: product_id => qty_to_ship
        ]);

        DB::transaction(function () use ($request, $so) {
            
            // A. Buat Header Shipment
            $shipment = Shipment::create([
                'shipment_number' => $request->shipment_number,
                'sales_order_id' => $so->id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            $totalShippedInThisSession = 0;

            // B. Loop Barang yang mau dikirim
            foreach ($request->items as $productId => $qtyToShip) {
                
                // Pastikan qty integer (untuk keamanan)
                $qtyToShip = (int) $qtyToShip;

                // Skip jika qty 0 atau negatif
                if ($qtyToShip <= 0) continue;

                // Ambil Detail SO terkait produk ini
                $soDetail = $so->details->where('product_id', $productId)->first();
                $product = Product::findOrFail($productId);

                if (!$soDetail) {
                    throw new \Exception("Produk ID {$productId} tidak ditemukan dalam pesanan ini.");
                }

                // Validasi 1: Jangan kirim melebihi pesanan
                $remainingQty = $soDetail->quantity - $soDetail->shipped_quantity;
                if ($qtyToShip > $remainingQty) {
                    throw new \Exception("Produk {$product->name}: Jumlah kirim ({$qtyToShip}) melebihi sisa pesanan ({$remainingQty}).");
                }

                // Validasi 2: Cek Stok Gudang
                if ($product->stock < $qtyToShip) {
                    throw new \Exception("Stok gudang tidak cukup untuk {$product->name}. Sisa fisik: {$product->stock}");
                }

                // C. EKSEKUSI PENGURANGAN STOK
                // 1. Kurangi Stok Fisik
                $product->decrement('stock', $qtyToShip); // Cara lebih aman concurrency daripada $p->stock -=

                // 2. Update Progress di SO Detail
                $soDetail->increment('shipped_quantity', $qtyToShip);

                // 3. Simpan Detail Shipment
                ShipmentDetail::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $productId,
                    'quantity' => $qtyToShip,
                ]);

                // 4. Catat Kartu Stok (History Log)
                Transaction::create([
                    'product_id' => $productId,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $qtyToShip,
                    'reference' => $shipment->shipment_number, // Referensi ke SJ
                    'notes' => 'Pengiriman Pesanan: ' . $so->so_number,
                ]);

                $totalShippedInThisSession++;
            }

            if ($totalShippedInThisSession == 0) {
                throw new \Exception("Tidak ada barang yang dikirim. Mohon isi jumlah kirim minimal satu barang.");
            }

            // D. Cek Status Akhir SO (Apakah Partial atau Full?)
            $allCompleted = true;
            $so->refresh(); // Refresh data terbaru dari DB setelah update quantity
            
            foreach ($so->details as $detail) {
                if ($detail->shipped_quantity < $detail->quantity) {
                    $allCompleted = false; // Masih ada sisa yang belum dikirim
                    break;
                }
            }

            // Update Status SO
            $so->update(['status' => $allCompleted ? 'shipped' : 'partial']);
        });

        return redirect()->route('sales.show', $salesOrderId)
            ->with('success', 'Pengiriman berhasil dibuat! Stok gudang telah dikurangi.');
    }

    // 4. CETAK SURAT JALAN (PDF)
    public function print($id)
    {
        // Load shipment beserta relasinya
        $shipment = Shipment::with([
            'salesOrder.customer', 
            'details.product', 
            'user'
        ])->findOrFail($id);
        
        $pdf = Pdf::loadView('shipments.print', compact('shipment'));
        $pdf->setPaper('a5', 'landscape'); // Ukuran A5 Landscape (Standar Surat Jalan)
        
        return $pdf->stream('SJ-' . $shipment->shipment_number . '.pdf');
    }
}