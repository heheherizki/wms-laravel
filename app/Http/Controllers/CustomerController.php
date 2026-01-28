<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // 1. DAFTAR CUSTOMER
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customers.index', compact('customers'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        return view('customers.create');
    }

    // 3. SIMPAN DATA
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:customers,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'payment_terms' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        // Default credit_limit ke 0 jika kosong
        if (!isset($validatedData['credit_limit']) || $validatedData['credit_limit'] === null) {
            $validatedData['credit_limit'] = 0;
        }

        Customer::create($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // 5. UPDATE DATA (DENGAN AUTO SYNC STATUS ORDER)
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validatedData = $request->validate([
            'code'          => 'required|string|unique:customers,code,' . $id,
            'name'          => 'required|string|max:255',
            'address'       => 'nullable|string',
            'phone'         => 'nullable|string',
            'email'         => 'nullable|email',
            'payment_terms' => 'nullable|string',
            'credit_limit'  => 'nullable|numeric|min:0',
        ]);

        // Default limit 0
        if (!isset($validatedData['credit_limit']) || $validatedData['credit_limit'] === null) {
            $validatedData['credit_limit'] = 0;
        }

        // 1. Update Data Customer
        $customer->update($validatedData);

        // 2. TRIGGER EVALUASI MASSAL (Big Switch)
        // Cek ulang semua order: 
        // - Jika limit naik -> Lepas Hold
        // - Jika limit turun -> Tahan Order Pending
        $customer->refreshOrderStatus();

        return redirect()->route('customers.index')
            ->with('success', 'Data diperbarui & Status Order seluruhnya telah disinkronisasi.');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer dihapus.');
    }

    // 7. FITUR UNLOCK (BYPASS LIMIT 1 JAM)
    public function unlock($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Berikan izin 1 jam
        $customer->update([
            'authorized_until' => now()->addHour(),
        ]);

        // TRIGGER EVALUASI MASSAL
        // Karena authorized_until aktif, fungsi ini otomatis akan melepas SEMUA hold.
        $customer->refreshOrderStatus();

        return back()->with('success', "Customer {$customer->name} di-unlock 1 Jam. Semua order otomatis dilepas.");
    }
}