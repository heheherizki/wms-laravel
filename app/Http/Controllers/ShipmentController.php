<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
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

    // 1. FORM PENGIRIMAN (Berdasarkan SO ID)
    public function create($salesOrderId)
    {
        $so = SalesOrder::with(['details.product', 'customer'])->findOrFail($salesOrderId);

        // Validasi: Kalau sudah selesai dikirim semua, tolak.
        if ($so->status == 'shipped') {
            return back()->with('error', 'Order ini sudah dikirim sepenuhnya.');
        }

        // Generate No Surat Jalan: SJ-YYYYMM-XXXX
        $lastShipment = Shipment::latest()->first();
        $nextId = $lastShipment ? $lastShipment->id + 1 : 1;
        $sjNumber = 'SJ-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('shipments.create', compact('so', 'sjNumber'));
    }

    // 2. PROSES SIMPAN PENGIRIMAN (LOGIKA PARTIAL)
    public function store(Request $request, $salesOrderId)
    {
        $so = SalesOrder::with('details')->findOrFail($salesOrderId);

        $request->validate([
            'date' => 'required|date',
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
                // Skip jika qty 0 atau kosong
                if ($qtyToShip <= 0) continue;

                // Ambil Detail SO terkait produk ini
                $soDetail = $so->details->where('product_id', $productId)->first();
                $product = Product::findOrFail($productId);

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
                $product->stock -= $qtyToShip;
                $product->save();

                // 2. Update Progress di SO Detail
                $soDetail->shipped_quantity += $qtyToShip;
                $soDetail->save();

                // 3. Simpan Detail Shipment
                ShipmentDetail::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $productId,
                    'quantity' => $qtyToShip,
                ]);

                // 4. Catat Kartu Stok (History)
                \App\Models\Transaction::create([
                    'product_id' => $productId,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $qtyToShip,
                    'reference' => $shipment->shipment_number, // Referensi ke SJ, bukan SO
                    'notes' => 'Pengiriman atas ' . $so->so_number,
                ]);

                $totalShippedInThisSession++;
            }

            if ($totalShippedInThisSession == 0) {
                throw new \Exception("Tidak ada barang yang dikirim. Mohon isi jumlah kirim.");
            }

            // D. Cek Status Akhir SO (Apakah Partial atau Full?)
            $allCompleted = true;
            $so->refresh(); // Refresh data terbaru
            foreach ($so->details as $detail) {
                if ($detail->shipped_quantity < $detail->quantity) {
                    $allCompleted = false; // Masih ada sisa
                    break;
                }
            }

            // Update Status SO
            $so->update(['status' => $allCompleted ? 'shipped' : 'partial']);
        });

        return redirect()->route('sales.show', $salesOrderId)->with('success', 'Pengiriman berhasil dibuat! Stok berkurang.');
    }

    // 3. PRINT SURAT JALAN (Berdasarkan Shipment ID)
    public function print($id)
    {
        $shipment = Shipment::with(['salesOrder.customer', 'details.product', 'user'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shipments.print', compact('shipment'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('SJ-' . $shipment->shipment_number . '.pdf');

        $filename = 'SJ-' . str_replace('/', '-', $shipment->shipment_number) . '.pdf';
        return $pdf->stream($filename);
    }
}