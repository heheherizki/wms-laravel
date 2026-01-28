<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class StatementController extends Controller
{
    public function index(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        // Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // --- 1. HITUNG SALDO AWAL (OPENING BALANCE) ---
        // Logic: Ambil semua transaksi SEBELUM tanggal mulai ($startDate)
        
        // A. Total Tagihan Lama (Invoice via SalesOrder)
        // Kita gunakan whereHas untuk menembus relasi: Invoice -> SalesOrder -> Customer
        $prevInvoices = Invoice::whereHas('salesOrder', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->where('date', '<', $startDate)
        ->sum('total_amount');

        // B. Total Bayar Lama (Payment via Invoice -> SalesOrder)
        // Relasi: Payment -> Invoice -> SalesOrder -> Customer
        $prevPayments = Payment::whereHas('invoice.salesOrder', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->where('date', '<', $startDate)
        ->sum('amount');

        // Saldo Awal = Tagihan Lama - Pembayaran Lama
        $openingBalance = $prevInvoices - $prevPayments;


        // --- 2. AMBIL TRANSAKSI PERIODE INI (MUTASI) ---
        
        // A. Ambil Invoice (Menambah Hutang / Debit)
        $invoices = Invoice::whereHas('salesOrder', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->whereBetween('date', [$startDate, $endDate])
        ->get()
        ->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'date' => $item->date,
                'type' => 'INVOICE',
                'description' => 'Tagihan No. ' . $item->invoice_number . ' (SO: ' . ($item->salesOrder->so_number ?? '-') . ')',
                'debit' => $item->total_amount, // Menambah Hutang
                'credit' => 0,
                'timestamp' => $item->created_at // Untuk sorting
            ];
        });

        // B. Ambil Payment (Mengurangi Hutang / Kredit)
        $payments = Payment::whereHas('invoice.salesOrder', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->whereBetween('date', [$startDate, $endDate])
        ->get()
        ->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'date' => $item->date,
                'type' => 'PAYMENT',
                'description' => 'Pembayaran No. ' . $item->payment_number . ' (' . $item->payment_method . ')',
                'debit' => 0,
                'credit' => $item->amount, // Mengurangi Hutang
                'timestamp' => $item->created_at // Untuk sorting
            ];
        });

        // --- 3. GABUNG DAN URUTKAN ---
        // Gabung Invoice & Payment jadi satu list transaksi
        $transactions = $invoices->concat($payments);
        
        // Urutkan berdasarkan Tanggal, lalu Jam dibuat
        $transactions = $transactions->sortBy(function ($t) {
            return $t->date . $t->timestamp;
        });

        return view('customers.statement', compact('customer', 'transactions', 'openingBalance', 'startDate', 'endDate'));
    }
}