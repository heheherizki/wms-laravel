<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Handle Barang Masuk (IN)
public function storeIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01', // Support desimal (0.5 dus)
            'unit'       => 'required|in:pcs,pack', // Validasi satuan
            'reference'  => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);
            
            // LOGIKA KONVERSI UNIT
            $qtyInput = $request->quantity;
            $actualQty = $qtyInput; // Default Pcs
            $noteUnit = 'Pcs';

            // Jika inputan adalah Dus/Pack
            if ($request->unit == 'pack') {
                // Cek validasi: Produk ini punya satuan pack gak?
                if (!$product->pack_quantity || $product->pack_quantity <= 1) {
                    return back()->with('error', 'Produk ini tidak memiliki satuan Dus/Pack!');
                }
                
                $actualQty = $qtyInput * $product->pack_quantity; // Kalikan isi dus
                $noteUnit = $product->pack_unit ?? 'Dus'; // Ambil nama satuan (Koli/Dus)
            }

            // 1. Catat History (Simpan info input asli di notes agar admin ingat)
            Transaction::create([
                'product_id' => $request->product_id,
                'type'       => 'in',
                'quantity'   => $actualQty, // Database tetap simpan Total Pcs (Base Unit)
                'reference'  => $request->reference ?? 'Penyesuaian Stok',
                'notes'      => "Input: {$qtyInput} {$noteUnit}. " . ($request->notes ?? ''),
                'user_id'    => auth()->id(),
            ]);

            // 2. Tambah Stok Produk
            $product->increment('stock', $actualQty);
        });

        return back()->with('success', 'Stok berhasil ditambahkan!');
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01',
            'unit'       => 'required|in:pcs,pack',
            'reference'  => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // LOGIKA KONVERSI UNIT
        $qtyInput = $request->quantity;
        $actualQty = $qtyInput;
        $noteUnit = 'Pcs';

        if ($request->unit == 'pack') {
             if (!$product->pack_quantity || $product->pack_quantity <= 1) {
                return back()->with('error', 'Produk ini tidak memiliki satuan Dus/Pack!');
            }
            $actualQty = $qtyInput * $product->pack_quantity;
            $noteUnit = $product->pack_unit ?? 'Dus';
        }

        // Cek Stok Cukup (Dalam Pcs)
        if ($product->stock < $actualQty) {
            return back()->with('error', "Stok tidak cukup! Sisa: {$product->stock_label}. (Permintaan: {$actualQty} Pcs)");
        }

        DB::transaction(function () use ($request, $product, $actualQty, $qtyInput, $noteUnit) {
            Transaction::create([
                'product_id' => $request->product_id,
                'type'       => 'out',
                'quantity'   => $actualQty,
                'reference'  => $request->reference ?? 'Keluar',
                'notes'      => "Input: {$qtyInput} {$noteUnit}. " . ($request->notes ?? ''),
                'user_id'    => auth()->id(),
            ]);

            $product->decrement('stock', $actualQty);
        });

        return back()->with('success', 'Stok berhasil dikurangi!');
    }
}