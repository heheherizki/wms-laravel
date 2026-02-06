<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchasePayment;
use App\Models\PurchaseDetail;

class PurchaseReturnController extends Controller
{
    // 1. DAFTAR RETUR PEMBELIAN
    public function index(Request $request)
    {
        // 1. Eager Loading Relasi
        $query = PurchaseReturn::with(['purchase.supplier', 'user']);

        // 2. Filter Pencarian (No Retur / Nama Supplier / No PO)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%") // Asumsi ID dipakai sebagai No Retur
                ->orWhereHas('purchase.supplier', function($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('purchase', function($p) use ($search) {
                    $p->where('po_number', 'like', "%{$search}%");
                });
            });
        }

        // 3. Filter Status (Multi-select support jika mau, disini single select dulu agar simpel)
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
            'pending_count' => PurchaseReturn::where('status', 'pending')->count(),
            'today_count' => PurchaseReturn::whereDate('date', now())->count(),
        ];

        return view('purchase_returns.index', compact('returns', 'stats'));
    }

    // 2. FORM PENGAJUAN (Pilih PO)
    public function create()
    {
        // Hanya PO yang sudah Completed (Barang sudah diterima) atau minimal sudah ada barang masuk
        // Tapi sederhananya kita ambil yang completed dulu
        $purchases = Purchase::where('status', 'completed')->latest()->get();
        return view('purchase_returns.create', compact('purchases'));
    }

    // 3. API: AMBIL BARANG DARI PO (Untuk Dropdown di Form)
    public function getReceivedProducts($purchaseId)
    {
        // Ambil detail barang yang SUDAH DITERIMA (quantity_received) dari PO ini
        $receivedItems = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->where('purchase_details.purchase_id', $purchaseId)
            ->where('purchase_details.quantity_received', '>', 0) // Hanya yg sudah diterima
            ->select(
                'products.id', 
                'products.name', 
                'products.sku', 
                'products.unit',
                'purchase_details.quantity_received' // Kuota maksimal retur
            )
            ->get();

        // (Opsional) Kurangi dengan yang SUDAH diretur sebelumnya agar tidak double return
        // Logic ini bisa ditambahkan nanti jika perlu validasi ketat

        return response()->json($receivedItems);
    }

    // 4. SIMPAN PENGAJUAN RETUR
    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'date' => 'required|date',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'reason' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // A. Header Retur
            $return = PurchaseReturn::create([
                'purchase_id' => $request->purchase_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'reason' => $request->reason,
                'status' => 'pending', // Butuh Approval Manager
            ]);

            // B. Detail Barang
            foreach ($request->products as $index => $productId) {
                $qty = $request->quantities[$index];
                if ($qty > 0) {
                    PurchaseReturnDetail::create([
                        'purchase_return_id' => $return->id,
                        'product_id' => $productId,
                        'quantity' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('purchase_returns.index')->with('success', 'Retur Pembelian diajukan. Menunggu Approval.');
    }

    // 5. DETAIL
    public function show($id)
    {
        $return = PurchaseReturn::with(['details.product', 'purchase.supplier', 'user'])->findOrFail($id);
        return view('purchase_returns.show', compact('return'));
    }

    // 6. APPROVE (STOK KELUAR & POTONG HUTANG)
    public function approve($id)
    {
        $return = PurchaseReturn::with(['details', 'purchase'])->findOrFail($id);
        
        if ($return->status !== 'pending') return back();

        DB::transaction(function () use ($return) {
            $totalRefundValue = 0; // Penampung total nilai uang

            foreach ($return->details as $detail) {
                // 1. AMBIL HARGA BELI ASLI DARI PO
                // Kita harus tahu dulu barang ini dulu dibeli dengan harga berapa
                $poItem = PurchaseDetail::where('purchase_id', $return->purchase_id)
                            ->where('product_id', $detail->product_id)
                            ->first();
                
                $price = $poItem ? $poItem->buy_price : 0;
                $subtotalRetur = $detail->quantity * $price;
                $totalRefundValue += $subtotalRetur;

                // 2. KURANGI STOK GUDANG
                $product = Product::findOrFail($detail->product_id);
                $product->stock -= $detail->quantity;
                $product->save();
                
                // 3. CATAT KARTU STOK
                if (class_exists(Transaction::class)) {
                    Transaction::create([
                        'product_id' => $detail->product_id,
                        'user_id' => Auth::id(),
                        'type' => 'out', 
                        'quantity' => $detail->quantity,
                        'reference' => 'Retur #' . $return->id,
                        'notes' => 'Retur ke Supplier (Potong Stok)',
                        'date' => now()
                    ]);
                }
            }

            // 4. UPDATE KEUANGAN (POTONG HUTANG / DEBIT NOTE)
            if ($totalRefundValue > 0) {
                // Catat sebagai "Pembayaran" (tapi metodenya Retur)
                PurchasePayment::create([
                    'purchase_id' => $return->purchase_id,
                    'user_id' => Auth::id(),
                    'date' => now(),
                    'amount' => $totalRefundValue,
                    'payment_method' => 'Debit Note (Retur)', // Penanda khusus
                    'notes' => 'Otomatis dari Retur Pembelian #' . $return->id,
                ]);

                // Update data PO Induk
                $po = $return->purchase;
                $po->amount_paid += $totalRefundValue; // Dianggap sudah "terbayar" lewat retur

                // Cek ulang status lunas
                if ($po->amount_paid >= ($po->total_amount - 1)) {
                    $po->payment_status = 'paid';
                } else {
                    $po->payment_status = 'partial'; // atau tetap partial
                }
                $po->save();
            }

            $return->update(['status' => 'approved']);
        });

        return back()->with('success', 'Retur disetujui: Stok berkurang & Hutang dipotong otomatis.');
    }

    // 7. REJECT
    public function reject($id)
    {
        $return = PurchaseReturn::findOrFail($id);
        if ($return->status == 'pending') {
            $return->update(['status' => 'rejected']);
        }
        return back()->with('success', 'Pengajuan retur ditolak.');
    }

    // 8. CETAK NOTA RETUR (Debit Note)
    public function print($id)
    {
        $return = PurchaseReturn::with(['details.product', 'purchase.supplier', 'user'])->findOrFail($id);
        $pdf = Pdf::loadView('purchase_returns.print', compact('return'));
        return $pdf->stream('Retur-Beli-'.$return->id.'.pdf');
    }
}