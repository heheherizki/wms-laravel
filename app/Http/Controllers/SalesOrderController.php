<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    // 1. TAMPILKAN LIST SALES
    public function index()
    {
        $orders = SalesOrder::with(['customer', 'user'])->latest()->get();
        return view('sales.index', compact('orders'));
    }

    // 2. FORM BUAT ORDER BARU
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        
        // Hanya tampilkan 'Barang Jadi' untuk dijual
        // Sparepart/Bahan Baku biasanya tidak dijual (kecuali settingan pabrikmu beda)
        $products = Product::where('type', 'finished_good')
                           ->where('stock', '>', 0) // Hanya tampilkan yg ada stok
                           ->orderBy('name')
                           ->get();

        // Generate No SO: SO-2026-0001
        $lastOrder = SalesOrder::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $soNumber = 'SO-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'products', 'soNumber'));
    }

    // 3. SIMPAN ORDER
    public function store(Request $request)
    {
        $request->validate([
            'so_number' => 'required|unique:sales_orders,so_number',
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // A. Header
            $so = SalesOrder::create([
                'so_number' => $request->so_number,
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
                'status' => 'pending', // Stok belum berkurang
                'payment_status' => 'unpaid',
                'grand_total' => 0,
            ]);

            $total = 0;

            // B. Details
            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;

                SalesOrderDetail::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Update Total
            $so->update(['grand_total' => $total]);
        });

        return redirect()->route('sales.index')->with('success', 'Sales Order berhasil dibuat!');
    }

    // 4. SHOW (Detail)
    public function show($id)
    {
        $order = SalesOrder::with(['customer', 'user', 'details.product'])->findOrFail($id);
        return view('sales.show', compact('order'));
    }

    // 1. PROSES PENGIRIMAN (KURANGI STOK)
    public function markAsShipped($id)
    {
        $so = SalesOrder::with('details')->findOrFail($id);

        if ($so->status != 'pending') {
            return back()->with('error', 'Pesanan ini sudah diproses atau dibatalkan.');
        }

        // Cek Stok Dulu (Cukup gak?)
        foreach ($so->details as $detail) {
            $product = \App\Models\Product::findOrFail($detail->product_id);
            if ($product->stock < $detail->quantity) {
                return back()->with('error', "Stok tidak cukup untuk produk: {$product->name}. Sisa: {$product->stock}");
            }
        }

        // Kalau Stok Aman, Lakukan Pengurangan
        \Illuminate\Support\Facades\DB::transaction(function () use ($so) {
            foreach ($so->details as $detail) {
                $product = \App\Models\Product::findOrFail($detail->product_id);
                $product->stock -= $detail->quantity; // Kurangi Stok
                $product->save();

                // Catat di History
                \App\Models\Transaction::create([
                    'product_id' => $product->id,
                    'user_id'    => \Illuminate\Support\Facades\Auth::id(),
                    'type'       => 'out',
                    'quantity'   => $detail->quantity,
                    'reference'  => 'SO: ' . $so->so_number,
                    'notes'      => 'Pengiriman Barang ke Customer',
                ]);
            }

            $so->update(['status' => 'shipped']);
        });

        return back()->with('success', 'Barang berhasil dikirim! Stok gudang telah dikurangi.');
    }

    // 2. CETAK SURAT JALAN (Tanpa Harga)
    public function printShipment($id)
    {
        $order = SalesOrder::with(['customer', 'details.product'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.print_shipment', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Surat_Jalan_' . $order->so_number . '.pdf');
    }

    // 3. CETAK INVOICE (Dengan Harga)
    public function printInvoice($id)
    {
        $order = SalesOrder::with(['customer', 'details.product'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.print_invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Invoice_' . $order->so_number . '.pdf');
    }

    // 4. HAPUS SO
    public function destroy($id)
    {
        $so = SalesOrder::findOrFail($id);
        if ($so->status != 'pending') {
            return back()->with('error', 'Hanya pesanan status Pending yang bisa dihapus.');
        }
        $so->delete();
        return redirect()->route('sales.index')->with('success', 'Sales Order dihapus.');
    }

    public function printSo($id)
    {
        // Load product agar bisa ambil rack_location
        $order = SalesOrder::with(['customer', 'details.product'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.print_so', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Picking_List_' . $order->so_number . '.pdf');

        $filename = 'Dokumen_' . str_replace('/', '-', $order->so_number) . '.pdf';
        return $pdf->stream($filename);
    }
}