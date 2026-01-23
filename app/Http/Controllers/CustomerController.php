<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:customers,code',
            'name' => 'required|string|max:255',
            'payment_terms' => 'required|integer', // Validasi angka
        ]);

        Customer::create($request->all());

        return back()->with('success', 'Customer berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:customers,code,' . $id,
            'name' => 'required|string|max:255',
            'payment_terms' => 'required|integer',
        ]);

        $customer->update($request->all());

        return back()->with('success', 'Data customer diperbarui!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return back()->with('success', 'Customer dihapus.');
    }
}