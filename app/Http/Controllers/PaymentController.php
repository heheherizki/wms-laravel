<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    // 1. FORM BAYAR (Hanya bisa diakses dari Detail Invoice)
    public function create($invoice_id)
    {
        $invoice = Invoice::with('salesOrder.customer')->findOrFail($invoice_id);
        
        if ($invoice->status == 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Invoice ini sudah lunas!');
        }

        return view('payments.create', compact('invoice'));
    }

    // 2. SIMPAN PEMBAYARAN & UPDATE STATUS INVOICE
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'     => 'required|exists:invoices,id',
            'date'           => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        // Validasi: Tidak boleh bayar lebih dari sisa tagihan
        // (Gunakan pembulatan float untuk menghindari error koma 0.00000001)
        if (round($request->amount, 2) > round($invoice->remaining_balance, 2)) {
            return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
        }

        DB::transaction(function () use ($request, $invoice) {
            // A. Simpan Payment
            Payment::create([
                'invoice_id'     => $invoice->id,
                'user_id'        => Auth::id(),
                'payment_number' => 'PAY-' . date('Ymd') . '-' . rand(100, 999),
                'date'           => $request->date,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'note'           => $request->note,
            ]);

            // B. Update Status Invoice Otomatis
            // PERBAIKAN: Cukup ambil sum dari database, JANGAN ditambah $request->amount lagi
            // Karena Payment::create di atas sudah memasukkan data ke database.
            
            $totalPaid = \App\Models\Payment::where('invoice_id', $invoice->id)->sum('amount');
            
            // Gunakan toleransi kecil untuk perbandingan float
            if ($totalPaid >= ($invoice->total_amount - 1)) { 
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partial']);
            }
        });

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Pembayaran berhasil dicatat. Status Invoice diperbarui.');
    }

    // 3. CETAK KWITANSI (RECEIPT)
    public function print($id)
    {
        $payment = Payment::with(['invoice.salesOrder.customer', 'user'])->findOrFail($id);
        
        $pdf = Pdf::loadView('payments.print', compact('payment'));
        return $pdf->stream('Kwitansi-' . $payment->payment_number . '.pdf');
    }

    // 4. HAPUS PEMBAYARAN (JIKA SALAH INPUT)
    public function destroy($id)
    {
        // Hanya admin yang boleh hapus uang
        if(Auth::user()->role !== 'admin') abort(403);

        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        $payment->delete();

        // Kembalikan status invoice
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid == 0) {
            $invoice->update(['status' => 'unpaid']);
        } elseif ($totalPaid < $invoice->total_amount) {
            $invoice->update(['status' => 'partial']);
        }

        return back()->with('success', 'Pembayaran dibatalkan/dihapus.');
    }
}