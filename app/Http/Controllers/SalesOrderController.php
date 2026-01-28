<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesOrderController extends Controller
{
    // 1. TAMPILKAN LIST SALES
    public function index()
    {
        $orders = SalesOrder::with(['customer', 'user'])->latest()->paginate(10);
        return view('sales.index', compact('orders'));
    }

    // 2. FORM BUAT ORDER BARU
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        
        // Hanya tampilkan 'Barang Jadi' (Finished Good) & Stok > 0
        $products = Product::where('type', 'finished_good')
                            ->where('stock', '>', 0)
                            ->orderBy('name')
                            ->get();

        // Generate No SO: SO-202601-0001
        $lastOrder = SalesOrder::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $soNumber = 'SO-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'products', 'soNumber'));
    }

    // 3. SIMPAN ORDER (DENGAN LOGIC CREDIT LIMIT & AGING HOLD)
    public function store(Request $request)
    {
        $request->validate([
            // ... validasi sama seperti sebelumnya ...
            'so_number' => 'required|unique:sales_orders,so_number',
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            
            // 1. Hitung Grand Total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += ($item['quantity'] * $item['price']);
            }

            // 2. SIMPAN ORDER SEBAGAI 'PENDING' (Optimis dulu)
            $so = \App\Models\SalesOrder::create([
                'so_number' => $request->so_number,
                'customer_id' => $request->customer_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
                'status' => 'pending', // <--- SELALU PENDING DULU
                'payment_status' => 'unpaid',
                'grand_total' => $grandTotal,
            ]);

            // 3. Simpan Detail
            foreach ($request->items as $item) {
                \App\Models\SalesOrderDetail::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }

            // 4. TRIGGER EVALUASI MASSAL (The Big Switch)
            // Suruh Customer cek ulang: "Eh ada order baru nih, limit kamu jebol gak? Kalau jebol, tahan SEMUA ya!"
            $so->customer->refreshOrderStatus();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sales Order dibuat. Status telah disesuaikan dengan limit customer.');
    }

    // 4. SHOW (Detail)
    public function show($id)
    {
        $order = SalesOrder::with(['customer', 'user', 'details.product'])->findOrFail($id);
        return view('sales.show', compact('order'));
    }

    // 5. APPROVE / RELEASE HOLD (KHUSUS ADMIN)
    public function approve($id)
    {
        // Pastikan hanya admin (sesuai middleware/logic auth Anda)
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang bisa melepas hold.');
        }

        $order = SalesOrder::findOrFail($id);
        
        // Berikan status PENDING dan IZIN SEMENTARA selama 1 JAM (60 Menit)
        $order->update([
            'status' => 'pending',
            'authorized_until' => now()->addHour(), 
        ]);

        return back()->with('success', 'Credit Hold berhasil dilepas. Gudang memiliki waktu 1 JAM untuk memproses pengiriman sebelum sistem mengunci kembali.');
    }

    // 6. HAPUS SO
    public function destroy($id)
    {
        $so = SalesOrder::findOrFail($id);
        
        // Hanya boleh hapus jika belum diproses (Shipped/Partial)
        if (in_array($so->status, ['shipped', 'partial'])) {
            return back()->with('error', 'Pesanan yang sudah ada pengiriman tidak bisa dihapus.');
        }

        $so->delete(); // Detail akan terhapus cascade (jika di migration di set cascade)
        return redirect()->route('sales.index')->with('success', 'Sales Order dihapus.');
    }

    // 7. CETAK PICKING LIST (Untuk Orang Gudang Siapkan Barang)
    public function printSo($id)
    {
        $order = SalesOrder::with(['customer', 'details.product'])->findOrFail($id);
        
        $pdf = Pdf::loadView('sales.print_so', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Picking_List_' . $order->so_number . '.pdf');
    }

    // RE-VALIDASI STATUS (AUTO CORRECTION)
    public function refreshStatus($id)
    {
        $order = SalesOrder::findOrFail($id);
        $customer = $order->customer;

        // 1. Cek Ulang Kondisi Keuangan Detik Ini
        $grandTotal = $order->grand_total;
        
        // Cek Limit (Sertakan order ini dalam perhitungan)
        $isOverLimit = $customer->credit_limit > 0 && ($customer->current_debt + $grandTotal > $customer->credit_limit);
        
        // Cek Overdue
        $isOverdue = $customer->hasOverdueInvoices();

        // 2. Ambil Keputusan
        if (!$isOverLimit && !$isOverdue) {
            // Jika ternyata SEHAT, kembalikan ke Pending
            $order->update(['status' => 'pending']);
            return back()->with('success', 'Sistem telah memverifikasi ulang: Customer SEHAT. Status order berhasil diubah menjadi PENDING.');
        } else {
            // Jika masih SAKIT
            return back()->with('error', 'Validasi Gagal: Customer masih memiliki masalah kredit (Over Limit atau Overdue). Tidak bisa dilepas.');
        }
    }
}