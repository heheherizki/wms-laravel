<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // 1. HALAMAN INDEX
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('suppliers.index', compact('suppliers'));
    }

    // 2. HALAMAN FORM TAMBAH
    public function create()
    {
        // Auto Generate Kode: SUP-001, SUP-002...
        $last = Supplier::latest()->first();
        // Ambil angka dari kode terakhir (asumsi format SUP-XXX)
        $number = 1;
        if($last) {
            // Ambil 3 digit terakhir
            $number = intval(substr($last->code, 4)) + 1;
        }
        $autoCode = 'SUP-' . str_pad($number, 3, '0', STR_PAD_LEFT);

        return view('suppliers.create', compact('autoCode'));
    }

    // 3. PROSES SIMPAN
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code'           => 'required|unique:suppliers,code',
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'phone'          => 'nullable|string',
            'email'          => 'nullable|email',
            'address'        => 'nullable|string',
            'term_days'      => 'nullable|integer|min:0', // Integrasi Keuangan
        ]);

        // Default term 0 (Cash) jika kosong
        if (!isset($validatedData['term_days'])) {
            $validatedData['term_days'] = 0;
        }

        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    // 4. HALAMAN FORM EDIT
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    // 5. PROSES UPDATE
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validatedData = $request->validate([
            'code'           => 'required|unique:suppliers,code,' . $id,
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'phone'          => 'nullable|string',
            'email'          => 'nullable|email',
            'address'        => 'nullable|string',
            'term_days'      => 'nullable|integer|min:0',
        ]);

        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Data supplier diperbarui.');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier dihapus.');
    }
}