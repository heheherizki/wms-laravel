<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\SalesOrder;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReturnController extends Controller
{
    // 1. DAFTAR RETUR
    public function index(Request $request)
    {
        // 1. Eager Loading Relasi
        $query = SalesReturn::with(['salesOrder.customer', 'user']);

        // 2. Filter Pencarian (No RMA / No SO / Customer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%") // Asumsi ID dipakai sebagai No RMA
                ->orWhereHas('salesOrder', function($so) use ($search) {
                    $so->where('so_number', 'like', "%{$search}%");
                })
                ->orWhereHas('salesOrder.customer', function($c) use ($search) {
                    $c->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 3. Filter Status (Single Select)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // 5. Eksekusi Data
        $returns = $query->latest()->paginate(10)->withQueryString();

        // 6. Statistik Ringkas
        $stats = [
            'pending_count' => SalesReturn::where('status', 'pending')->count(),
            'today_count' => SalesReturn::whereDate('date', now())->count(),
        ];

        return view('returns.index', compact('returns', 'stats'));
    }

    // 2. FORM PENGAJUAN
    public function create()
    {
        // Hanya SO yang sudah dikirim/partial
        $orders = SalesOrder::whereIn('status', ['shipped', 'partial'])->latest()->get();
        return view('returns.create', compact('orders'));
    }

    // 3. API: CEK KUOTA RETUR (PERBAIKAN LOGIKA)
    public function getShippedProducts($salesOrderId)
    {
        // A. Ambil Total yang Dikirim (Shipped)
        $shipped = DB::table('shipment_details')
            ->join('shipments', 'shipment_details.shipment_id', '=', 'shipments.id')
            ->where('shipments.sales_order_id', $salesOrderId)
            ->select('product_id', DB::raw('SUM(quantity) as total_shipped'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id'); // Index by product_id biar mudah

        // B. Ambil Total yang SUDAH Diretur (Approved/Pending) untuk SO ini
        // Kita harus menghitung yang 'pending' juga agar tidak double entry
        $returned = DB::table('sales_return_details')
            ->join('sales_returns', 'sales_return_details.sales_return_id', '=', 'sales_returns.id')
            ->where('sales_returns.sales_order_id', $salesOrderId)
            ->whereIn('sales_returns.status', ['pending', 'approved']) // Tolak/Rejected tidak dihitung
            ->select('product_id', DB::raw('SUM(quantity) as total_returned'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // C. Gabungkan Data (Hitung Sisa)
        $products = Product::whereIn('id', $shipped->keys())->get()->map(function($product) use ($shipped, $returned) {
            $qtyShipped = $shipped[$product->id]->total_shipped ?? 0;
            $qtyReturned = $returned[$product->id]->total_returned ?? 0;
            $remaining = $qtyShipped - $qtyReturned;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'total_shipped' => $qtyShipped,
                'total_returned' => $qtyReturned,
                'remaining_qty' => $remaining // INI YANG PENTING
            ];
        });

        // Filter: Hanya kirim produk yang masih bisa diretur (sisa > 0)
        return response()->json($products->where('remaining_qty', '>', 0)->values());
    }

    // 4. SIMPAN DENGAN VALIDASI KUOTA
    public function store(Request $request)
    {
        $request->validate([
            'sales_order_id' => 'required',
            'date' => 'required|date',
            'products' => 'required|array',
            'quantities' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            // A. Buat Header
            $return = SalesReturn::create([
                'sales_order_id' => $request->sales_order_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // B. Validasi & Simpan Detail
            foreach ($request->products as $index => $productId) {
                $qtyRequest = $request->quantities[$index];

                if ($qtyRequest > 0) {
                    // VALIDASI BACKEND: Cek lagi kuota (Jangan percaya frontend 100%)
                    $maxReturnable = $this->calculateMaxReturnable($request->sales_order_id, $productId);
                    
                    if ($qtyRequest > $maxReturnable) {
                        throw new \Exception("Jumlah retur melebihi barang yang dikirim/sisa kuota untuk produk ID: $productId");
                    }

                    SalesReturnDetail::create([
                        'sales_return_id' => $return->id,
                        'product_id' => $productId,
                        'quantity' => $qtyRequest,
                    ]);
                }
            }
        });

        return redirect()->route('returns.index')->with('success', 'Pengajuan retur berhasil. Menunggu Approval.');
    }

    // 5. DETAIL
    public function show($id)
    {
        $return = SalesReturn::with(['details.product', 'salesOrder.customer', 'user'])->findOrFail($id);
        return view('returns.show', compact('return'));
    }

    // 6. APPROVE
    public function approve($id)
    {
        $return = SalesReturn::with('details')->findOrFail($id);
        if ($return->status !== 'pending') return back();

        DB::transaction(function () use ($return) {
            foreach ($return->details as $detail) {
                Product::findOrFail($detail->product_id)->increment('stock', $detail->quantity);
                
                Transaction::create([
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $detail->quantity,
                    'reference' => 'Retur RMA #' . $return->id,
                ]);
            }
            $return->update(['status' => 'approved']);
        });

        return back()->with('success', 'Retur disetujui & Stok bertambah.');
    }

    // 7. REJECT
    public function reject($id)
    {
        $return = SalesReturn::findOrFail($id);
        if ($return->status == 'pending') {
            $return->update(['status' => 'rejected']);
        }
        return back()->with('success', 'Retur ditolak.');
    }

    // 8. PRINT RMA FORM (BARU)
    public function print($id)
    {
        $return = SalesReturn::with(['details.product', 'salesOrder.customer', 'user'])->findOrFail($id);
        $pdf = Pdf::loadView('returns.print', compact('return'));
        return $pdf->stream('RMA-'.$return->id.'.pdf');
    }

    // 9. DELETE (BATALKAN PENGAJUAN)
    public function destroy($id)
    {
        $return = SalesReturn::findOrFail($id);
        
        // Hanya boleh hapus jika status Pending
        if ($return->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan Pending yang bisa dihapus.');
        }

        $return->delete(); // Detail akan terhapus otomatis (Cascade) jika migration benar
        return back()->with('success', 'Pengajuan retur berhasil dihapus.');
    }

    // Helper Private untuk Hitung Sisa Kuota (Backend Security)
    private function calculateMaxReturnable($soId, $productId)
    {
        $shipped = DB::table('shipment_details')
            ->join('shipments', 'shipment_details.shipment_id', '=', 'shipments.id')
            ->where('shipments.sales_order_id', $soId)
            ->where('shipment_details.product_id', $productId)
            ->sum('quantity');

        // Kurangi dengan yang SUDAH diretur (Approved) atau SEDANG diajukan (Pending) selain retur ini
        $returned = DB::table('sales_return_details')
            ->join('sales_returns', 'sales_return_details.sales_return_id', '=', 'sales_returns.id')
            ->where('sales_returns.sales_order_id', $soId)
            ->whereIn('sales_returns.status', ['pending', 'approved'])
            ->where('sales_return_details.product_id', $productId)
            ->sum('quantity');

        return $shipped - $returned;
    }
}