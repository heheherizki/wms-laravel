<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // --- 1. FILTER BARU: TABS TIPE PRODUK (ERP) ---
        // Menangkap parameter ?type=... dari Tab Navigasi
        $type = $request->query('type');
        
        if ($type) {
            if ($type == 'material') {
                // Tab Sparepart & Bahan Baku digabung
                $query->whereIn('type', ['sparepart', 'raw_material']);
            } else {
                // Tab Barang Jadi
                $query->where('type', $type);
            }
        }

        // --- 2. FILTER LAMA: Search (Nama / SKU) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // --- 3. FILTER LAMA: Merk (Brand) ---
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // --- 4. FILTER LAMA: Lokasi Rak ---
        if ($request->filled('location')) {
            $query->where('rack_location', $request->location);
        }

        // --- 5. FILTER LAMA: Status Stok ---
        if ($request->filled('status')) {
            if ($request->status == 'habis') {
                $query->where('stock', 0);
            } elseif ($request->status == 'low') {
                $query->where('stock', '>', 0)
                      ->whereColumn('stock', '<=', 'min_stock');
            } elseif ($request->status == 'normal') {
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        // --- 6. SORTING ---
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'lowest':
                    $query->orderByRaw('(stock / COALESCE(NULLIF(pack_quantity, 0), 1)) ASC');
                    break;
                case 'highest':
                    $query->orderByRaw('(stock / COALESCE(NULLIF(pack_quantity, 0), 1)) DESC');
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->orderBy('name', 'asc'); // Default urut abjad nama
                    break;
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        // Eksekusi Data
        $products = $query->paginate(10)->withQueryString();

        // Data untuk Dropdown Filter
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->orderBy('brand')->pluck('brand');
        $locations = Product::select('rack_location')->distinct()->whereNotNull('rack_location')->orderBy('rack_location')->pluck('rack_location');

        // Eksekusi Data
        $products = $query->paginate(10)->withQueryString();

        // Data untuk Dropdown Filter
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->orderBy('brand')->pluck('brand');
        $locations = Product::select('rack_location')->distinct()->whereNotNull('rack_location')->orderBy('rack_location')->pluck('rack_location');

        // === TAMBAHAN DATA UTK MODAL TRANSAKSI ===
        // Kita ambil semua supplier dan customer untuk pilihan di modal
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();

        // Kirim $type ke view agar Tabs bisa mendeteksi mana yang aktif
        return view('products.index', compact('products', 'brands', 'locations', 'type', 'suppliers', 'customers'));
        
    }

    public function store(Request $request)
    {
        // Validasi Lengkap (Data Dasar + Data ERP)
        $request->validate([
            // Data Dasar
            'name'      => 'required|string|max:255',
            'sku'       => 'required|string|unique:products,sku',
            'category'  => 'required|string',
            'unit'      => 'nullable|string', 
            
            // Data ERP (Tipe & Harga)
            'type'      => 'required|in:finished_good,sparepart,raw_material',
            'buy_price' => 'nullable|numeric|min:0',
            'sell_price'=> 'nullable|numeric|min:0',

            // Data Gudang & Stok
            'min_stock' => 'required|integer',
            'rack_location' => 'nullable|string',
            
            // Opsi Tambahan
            'brand'     => 'nullable|string',
            'watt'      => 'nullable|string',
            'pack_unit' => 'nullable|string',
            'pack_quantity' => 'nullable|integer',
        ]);

        Product::create($request->all());

        return back()->with('success', 'Item baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validasi Update (Gabungan logika lama & baru)
        $request->validate([
            // Validasi Umum
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|unique:products,sku,' . $id, // Ignore ID sendiri
            'type'          => 'required|in:finished_good,sparepart,raw_material',
            'category'      => 'required|string',
            
            // Validasi Harga
            'buy_price'     => 'nullable|numeric|min:0',
            'sell_price'    => 'nullable|numeric|min:0',

            // Validasi Stok Teknis (Logika Lama Kamu)
            'min_stock'     => 'required|integer|min:0',
            'rack_location' => 'nullable|string|max:50',
            'pack_unit'     => 'nullable|string|max:20', 
            'pack_quantity' => 'nullable|integer|min:1',
        ]);

        // Ambil semua data request
        $data = $request->all();

        // --- LOGIKA CERDAS MIN STOCK (Dari kode lamamu) ---
        // Jika user input min_stock via Pack/Dus, kita konversi ke Pcs otomatis
        // Note: Pastikan di Form Edit name inputnya 'min_stock_unit' jika ingin fitur ini jalan
        // Jika tidak ada input 'min_stock_unit', dia pakai angka mentah.
        if ($request->has('min_stock_unit') && $request->min_stock_unit == 'pack') {
            if ($request->pack_quantity > 1) {
                $data['min_stock'] = $request->min_stock * $request->pack_quantity;
            }
        }

        $product->update($data);

        return back()->with('success', 'Data produk berhasil diperbarui!');
    }

    public function history($id)
    {
        // Pastikan model Transaction di-import atau gunakan full namespace \App\Models\Transaction
        $transactions = \App\Models\Transaction::with('user') // Load nama user
            ->where('product_id', $id)
            ->latest() // Urutkan dari yang terbaru
            ->take(50) // Ambil 50 saja biar cepat
            ->get();

        // Kembalikan dalam bentuk JSON (Data Mentah)
        return response()->json($transactions);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Cek stok fisik sebelum hapus
        if ($product->stock > 0) {
            return back()->with('error', 'Gagal hapus! Produk masih memiliki stok fisik ' . $product->stock);
        }

        $product->delete(); 

        return back()->with('success', 'Produk berhasil dihapus (diarsipkan).');
    }

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