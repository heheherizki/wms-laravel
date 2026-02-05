<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    // =========================================================================
    // 1. TAMPILKAN DAFTAR PRODUK (INDEX)
    // =========================================================================
    public function index(Request $request)
    {
        $query = Product::query();

        // --- FILTER 1: PENCARIAN (Search) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // --- FILTER 2: TIPE PRODUK ---
        $type = $request->query('type');
        if ($type) {
            if ($type == 'material') {
                $query->whereIn('type', ['sparepart', 'raw_material']);
            } else {
                $query->where('type', $type);
            }
        }

        // --- FILTER 3: MERK (Brand) ---
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // --- FILTER 4: LOKASI RAK ---
        if ($request->filled('location')) {
            $query->where('rack_location', $request->location);
        }

        // --- FILTER 5: STATUS STOK ---
        if ($request->filled('status')) {
            if ($request->status == 'out') {
                // Stok Habis (0 atau minus)
                $query->where('stock', '<=', 0);
            } elseif ($request->status == 'low') {
                // Low Stock (Ada tapi di bawah minimum)
                $query->where('stock', '>', 0)
                      ->whereColumn('stock', '<=', 'min_stock');
            } elseif ($request->status == 'normal') {
                // Stok Aman
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        // --- FILTER 6: SORTING ---
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'lowest':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'highest':
                    $query->orderBy('stock', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        // --- EKSEKUSI DATA (Pagination) ---
        $products = $query->paginate(10)->withQueryString();

        // --- DATA UNTUK DROPDOWN FILTER ---
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->orderBy('brand')->pluck('brand');
        $locations = Product::select('rack_location')->distinct()->whereNotNull('rack_location')->orderBy('rack_location')->pluck('rack_location');

        // --- DATA UNTUK MODAL POPUP (TRANSAKSI CEPAT) ---
        // Jika Anda masih pakai popup untuk Barang Masuk/Keluar di Index
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('products.index', compact('products', 'brands', 'locations', 'type', 'suppliers', 'customers'));
    }

    // =========================================================================
    // 2. HALAMAN FORM BARU (CREATE)
    // =========================================================================
    public function create()
    {
        return view('products.create');
    }

    // =========================================================================
    // 3. PROSES SIMPAN (STORE)
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            // Data Utama
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|unique:products,sku',
            'type'          => 'required|in:finished_good,sparepart,raw_material',
            'category'      => 'required|string',
            'unit'          => 'required|string',
            
            // Harga
            'buy_price'     => 'nullable|numeric|min:0',
            'sell_price'    => 'nullable|numeric|min:0',

            // Stok & Lokasi
            'min_stock'     => 'required|integer|min:0',
            'rack_location' => 'nullable|string',
            
            // Opsi Kemasan
            'pack_unit'     => 'nullable|string',
            'pack_quantity' => 'nullable|integer|min:1',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // =========================================================================
    // 4. HALAMAN EDIT
    // =========================================================================
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // =========================================================================
    // 5. PROSES UPDATE
    // =========================================================================
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|unique:products,sku,' . $id,
            'type'          => 'required|in:finished_good,sparepart,raw_material',
            'min_stock'     => 'required|integer|min:0',
            'buy_price'     => 'nullable|numeric|min:0',
            'sell_price'    => 'nullable|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    // =========================================================================
    // 6. API HISTORY (JSON untuk Popup)
    // =========================================================================
    public function history($id)
    {
        $product = Product::findOrFail($id);
        
        // Ambil transaksi terkait produk ini, urutkan dari terbaru, dan paginasi
        $transactions = \App\Models\Transaction::with('user')
            ->where('product_id', $id)
            ->latest()
            ->paginate(20); // Tampilkan 20 baris per halaman

        return view('products.history', compact('product', 'transactions'));
    }

    // =========================================================================
    // 7. HAPUS PRODUK (DESTROY)
    // =========================================================================
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Proteksi: Jangan hapus jika stok masih ada
        if ($product->stock > 0) {
            return back()->with('error', 'Gagal hapus! Produk masih memiliki stok fisik ' . $product->stock);
        }

        $product->delete(); 

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    // =========================================================================
    // 8. CETAK BARCODE
    // =========================================================================
    public function printBarcode(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $jumlahCetak = $request->qty ?? 1;

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($product->sku, $generator::TYPE_CODE_128);
        $barcodeBase64 = base64_encode($barcodeData);

        $pdf = Pdf::loadView('products.barcode', compact('product', 'barcodeBase64', 'jumlahCetak'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('barcode-' . $product->sku . '.pdf');
    }
}