<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk Database Transaction
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 1. Tampilkan Daftar PO
    public function index()
    {
        // Ambil data PO beserta nama Supplier dan User pembuatnya
        $purchases = Purchase::with(['supplier', 'user'])->latest()->get();
        return view('purchases.index', compact('purchases'));
    }

    // 2. Halaman Buat PO Baru
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        // Ambil produk yang tipenya BUKAN 'finished_good' (Jadi cuma Sparepart/Bahan Baku)
        // Kalau mau beli barang jadi untuk dijual lagi (Trading), hapus filter whereNotIn ini.
        $products = Product::whereNotIn('type', ['finished_good'])->orderBy('name')->get();
        
        // Generate No PO Otomatis (Format: PO-YYYYMM-XXXX)
        $lastPo = Purchase::latest()->first();
        $nextId = $lastPo ? $lastPo->id + 1 : 1;
        $poNumber = 'PO-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'poNumber'));
    }

    // 3. Simpan PO ke Database
    public function store(Request $request)
    {
        // Validasi Header & Detail
        $request->validate([
            'po_number' => 'required|unique:purchases,po_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1', // Harus ada minimal 1 barang
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ]);

        // Gunakan DB Transaction agar data aman (Semua tersimpan atau Batal semua)
        DB::transaction(function () use ($request) {
            // A. Simpan Header PO
            $purchase = Purchase::create([
                'po_number' => $request->po_number,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
                'status' => 'pending', // Status awal selalu Pending
                'total_amount' => 0, // Nanti diupdate setelah hitung detail
            ]);

            $totalAmount = 0;

            // B. Simpan Detail Barang (Looping)
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['buy_price'];
                $totalAmount += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // C. Update Total Amount di Header
            $purchase->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    // 4. Lihat Detail PO
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'details.product'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    // 5. Hapus PO (Hanya jika status masih pending)
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status != 'pending') {
            return back()->with('error', 'PO yang sudah selesai tidak bisa dihapus. Lakukan Retur jika perlu.');
        }

        $purchase->delete(); // Detail otomatis terhapus karena cascade di migration

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function markAsCompleted($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        // Cek status, jangan sampai diproses 2x
        if ($purchase->status != 'pending') {
            return back()->with('error', 'PO ini sudah diproses atau dibatalkan sebelumnya.');
        }

        // Mulai Database Transaction (Biar aman, kalau gagal 1, gagal semua)
        \Illuminate\Support\Facades\DB::transaction(function () use ($purchase) {
            
            // A. Update Stok Setiap Barang
            foreach ($purchase->details as $detail) {
                $product = \App\Models\Product::findOrFail($detail->product_id);
                $product->stock += $detail->quantity; // Tambah Stok
                
                // Update Harga Beli Master Data (Opsional: update harga beli terbaru)
                // $product->buy_price = $detail->buy_price; 
                
                $product->save();

                // (Opsional) Catat juga ke tabel 'transactions' history agar tercatat di kartu stok
                \App\Models\Transaction::create([
                    'product_id' => $product->id,
                    'user_id'    => \Illuminate\Support\Facades\Auth::id(),
                    'type'       => 'in',
                    'quantity'   => $detail->quantity,
                    'reference'  => 'PO: ' . $purchase->po_number,
                    'notes'      => 'Penerimaan Barang dari PO (Auto)',
                ]);
            }

            // B. Ubah Status PO jadi Completed
            $purchase->update(['status' => 'completed']);
        });

        return back()->with('success', 'Barang diterima! Stok otomatis bertambah.');
    }

    // METHOD CETAK PDF
    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'details.product'])->findOrFail($id);
        
        // Kita gunakan view khusus untuk PDF (bukan view HTML biasa)
        // karena PDF butuh layout kertas A4 yang fix
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchases.pdf', compact('purchase'));
        
        $pdf->setPaper('a4', 'portrait');
        
        // stream() berarti membuka di browser (preview), kalau download() langsung unduh file
        return $pdf->stream('PO-' . $purchase->po_number . '.pdf');

        $filename = 'PO-' . str_replace('/', '-', $purchase->po_number) . '.pdf';
        return $pdf->stream($filename);
    }

    public function edit($id)
    {
        $purchase = Purchase::with('details')->findOrFail($id);

        // Proteksi: Hanya PO Pending yang boleh diedit
        if ($purchase->status != 'pending') {
            return redirect()->route('purchases.index')->with('error', 'PO yang sudah selesai tidak bisa diedit.');
        }

        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        // Ambil produk selain barang jadi (raw material/sparepart)
        $products = \App\Models\Product::whereNotIn('type', ['finished_good'])->orderBy('name')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    // 2. SIMPAN PERUBAHAN
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        // Proteksi lagi
        if ($purchase->status != 'pending') {
            return back()->with('error', 'Gagal update! Status PO bukan Pending.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $purchase) {
            // A. Update Header
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'date' => $request->date,
                'notes' => $request->notes,
                // Total amount nanti diupdate di bawah
            ]);

            // B. RESET DETAIL: Hapus semua detail lama, ganti yang baru (Cara paling bersih)
            $purchase->details()->delete();

            $totalAmount = 0;

            // C. Simpan Detail Baru
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['buy_price'];
                $totalAmount += $subtotal;

                \App\Models\PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // D. Update Total Baru
            $purchase->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchases.show', $id)->with('success', 'Purchase Order berhasil diperbarui!');
    }

}