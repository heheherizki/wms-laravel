<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // 1. Tampilkan Daftar Supplier
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('suppliers.index', compact('suppliers'));
    }

    // 2. Simpan Supplier Baru
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:suppliers,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return back()->with('success', 'Supplier berhasil ditambahkan!');
    }

    // 3. Update Supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:suppliers,code,' . $id, // Ignore unique self
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        $supplier->update($request->all());

        return back()->with('success', 'Data supplier diperbarui!');
    }

    // 4. Hapus Supplier
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return back()->with('success', 'Supplier dihapus.');
    }
}