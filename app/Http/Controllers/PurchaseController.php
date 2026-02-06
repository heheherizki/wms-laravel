<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Transaction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    // 1. TAMPILKAN DAFTAR PO
    public function index(Request $request)
    {
        // 1. Query Dasar & Eager Loading
        $query = Purchase::with(['supplier', 'user']);

        // 2. Filter Pencarian (No PO / Supplier)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                ->orWhereHas('supplier', function($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 3. Filter Status PO (Multi-select)
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // 4. Filter Status Pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // 5. Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // 6. Eksekusi Data
        $purchases = $query->latest()->paginate(10)->withQueryString();

        // 7. Statistik Ringkas
        $stats = [
            'today_count' => Purchase::whereDate('date', now())->count(),
            'pending_po' => Purchase::where('status', 'pending')->count(),
            'total_debt' => Purchase::whereIn('payment_status', ['unpaid', 'partial'])
                            ->get()
                            ->sum(function($po) {
                                return $po->total_amount - $po->amount_paid;
                            }),
        ];

        return view('purchases.index', compact('purchases', 'stats'));
    }

    // 2. HALAMAN BUAT PO BARU
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(); // Semua produk bisa dibeli
        
        // Auto Number: PO-YYYYMM-0001
        $lastPo = Purchase::latest()->first();
        $number = 1;
        if ($lastPo) {
            $parts = explode('-', $lastPo->po_number);
            if (count($parts) == 3) {
                $number = intval($parts[2]) + 1;
            }
        }
        $poNumber = 'PO-' . date('Ym') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'poNumber'));
    }

    // 3. SIMPAN PO (DRAFT)
    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|unique:purchases,po_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // A. Simpan Header
            $purchase = Purchase::create([
                'po_number' => $request->po_number,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes, 
                'status' => 'pending', 
                
                // Finance Defaults
                'payment_status' => 'unpaid', 
                'amount_paid' => 0,           
                'total_amount' => 0,           
            ]);

            $grandTotal = 0;

            // B. Simpan Detail
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['buy_price'];
                $grandTotal += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_received' => 0, // Belum ada yang diterima
                    'buy_price' => $item['buy_price'], 
                    'subtotal' => $subtotal,
                ]);
            }

            // C. Update Total
            $purchase->update(['total_amount' => $grandTotal]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dibuat.');
    }

    // 4. LIHAT DETAIL PO
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product', 'payments.user'])->findOrFail($id);
        
        // TAMBAHAN: Ambil data akun keuangan untuk dropdown pembayaran
        $accounts = \App\Models\CashAccount::orderBy('name')->get(); 

        return view('purchases.show', compact('purchase', 'accounts'));
    }

    // 5. EDIT PO (Hanya jika belum ada barang diterima)
    public function edit($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        // Proteksi: Tidak boleh edit jika sudah ada barang masuk (walau cuma 1)
        $hasReceived = $purchase->details->sum('quantity_received') > 0;

        if ($purchase->status == 'completed' || $hasReceived) {
            return redirect()->route('purchases.index')->with('error', 'PO yang sudah diproses (barang masuk) tidak bisa diedit.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    // 6. UPDATE PO
    public function update(Request $request, $id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        $hasReceived = $purchase->details->sum('quantity_received') > 0;
        if ($purchase->status == 'completed' || $hasReceived) {
            return back()->with('error', 'Gagal update! Barang sudah masuk sebagian/semua.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            // Hapus detail lama, ganti baru
            $purchase->details()->delete();

            $grandTotal = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['buy_price'];
                $grandTotal += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_received' => 0,
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update(['total_amount' => $grandTotal]);
        });

        return redirect()->route('purchases.show', $id)->with('success', 'Purchase Order berhasil diperbarui.');
    }

    // 7. FORM PENERIMAAN BARANG (PARTIAL RECEIVE)
    public function receive($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);
        
        // Cek apakah sudah selesai semua
        $isAllReceived = $purchase->details->every(function($detail) {
            return $detail->quantity_received >= $detail->quantity;
        });

        if ($purchase->status == 'completed' || $isAllReceived) {
            return back()->with('error', 'Semua barang di PO ini sudah diterima sepenuhnya.');
        }

        return view('purchases.receive', compact('purchase'));
    }

    // 8. PROSES SIMPAN PENERIMAAN (LOGIKA INTI)
    public function processReceive(Request $request, $id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);
        
        $request->validate([
            'received_qty' => 'required|array',
            'received_qty.*' => 'numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $allCompleted = true; // Flag untuk cek lunas barang

            foreach ($purchase->details as $detail) {
                $incomingQty = $request->received_qty[$detail->id] ?? 0;

                if ($incomingQty > 0) {
                    // Validasi over-receive
                    $sisa = $detail->quantity - $detail->quantity_received;
                    if ($incomingQty > $sisa) {
                        throw new \Exception("Qty diterima melebih pesanan untuk produk: " . $detail->product->name);
                    }

                    // 1. Update Detail PO
                    $detail->quantity_received += $incomingQty;
                    $detail->save();

                    // 2. Update Stok & HPP di Master Product
                    $product = Product::findOrFail($detail->product_id);
                    $product->stock += $incomingQty;
                    $product->purchase_price = $detail->buy_price; // Update HPP terakhir
                    $product->save();

                    // 3. Catat Kartu Stok
                    if (class_exists(Transaction::class)) {
                        Transaction::create([
                            'product_id' => $product->id,
                            'user_id'    => Auth::id(),
                            'type'       => 'in',
                            'quantity'   => $incomingQty,
                            'reference'  => $purchase->po_number,
                            'notes'      => 'Inbound PO (Partial)',
                            'date'       => now(),
                        ]);
                    }
                }

                // Cek apakah item ini sudah lengkap
                if ($detail->quantity_received < $detail->quantity) {
                    $allCompleted = false;
                }
            }

            // 4. Update Status PO
            if ($allCompleted) {
                $purchase->update(['status' => 'completed']);
            } 
            // Jika belum completed, status tetap 'pending' (artinya masih ada sisa yg belum datang)
        });

        return redirect()->route('purchases.show', $id)->with('success', 'Penerimaan barang berhasil dicatat.');
    }

    // 9. HAPUS / BATALKAN PO
    public function destroy($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        $hasReceived = $purchase->details->sum('quantity_received') > 0;

        if ($purchase->status == 'completed' || $hasReceived) {
            return back()->with('error', 'PO sudah ada barang masuk. Tidak bisa dihapus. Lakukan Retur jika perlu.');
        }

        $purchase->update(['status' => 'canceled']);

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil DIBATALKAN.');
    }

    // 10. CETAK PDF
    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'details.product'])->findOrFail($id);
        $pdf = Pdf::loadView('purchases.pdf', compact('purchase'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('PO-' . $purchase->po_number . '.pdf');
    }
}