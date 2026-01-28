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
    // 1. FORM BAYAR
    public function create($invoice_id)
    {
        $invoice = Invoice::with('salesOrder.customer')->findOrFail($invoice_id);
        
        if ($invoice->status == 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Invoice ini sudah lunas!');
        }

        return view('payments.create', compact('invoice'));
    }

    // 2. SIMPAN PEMBAYARAN (DENGAN AUTO RELEASE HOLD)
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'     => 'required|exists:invoices,id',
            'date'           => 'required|date',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'note'           => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        
        // Validasi Sisa Tagihan
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

            // B. Update Status Invoice
            $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');
            
            if ($totalPaid >= ($invoice->total_amount - 1)) { 
                $invoice->update(['status' => 'paid']);
            } else {
                $invoice->update(['status' => 'partial']);
            }

            // C. Sinkronisasi Status SO
            $so = $invoice->salesOrder; 
            
            if ($so) {
                $allInvoices = $so->invoices; 
                $allPaid = true;
                $anyPaid = false;

                foreach ($allInvoices as $inv) {
                    $inv->refresh(); 
                    if ($inv->status != 'paid') $allPaid = false;
                    if ($inv->status == 'paid' || $inv->status == 'partial') $anyPaid = true;
                }

                if ($allPaid && $allInvoices->count() > 0) {
                    $so->update(['payment_status' => 'paid']);
                } elseif ($anyPaid) {
                    $so->update(['payment_status' => 'partial']);
                }

                // D. TRIGGER EVALUASI MASSAL (KUNCI UTAMA)
                // Karena ada uang masuk, kondisi keuangan customer membaik.
                // Panggil refreshOrderStatus() untuk melepas order-order lain yang tertahan.
                if ($so->customer) {
                    $so->customer->refreshOrderStatus();
                }
            }
        });

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Pembayaran berhasil dicatat. Status Credit Hold customer telah dievaluasi ulang.');
    }

    // 3. CETAK KWITANSI
    public function print($id)
    {
        $payment = Payment::with(['invoice.salesOrder.customer', 'user'])->findOrFail($id);
        $pdf = Pdf::loadView('payments.print', compact('payment'));
        return $pdf->stream('Kwitansi-' . $payment->payment_number . '.pdf');
    }

    // 4. HAPUS PEMBAYARAN
    public function destroy($id)
    {
        if(Auth::user()->role !== 'admin') abort(403);

        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;
        $so = $invoice->salesOrder;

        DB::transaction(function () use ($payment, $invoice, $so) {
            $payment->delete();

            // Kembalikan status invoice
            $totalPaid = $invoice->payments()->sum('amount');
            if ($totalPaid == 0) {
                $invoice->update(['status' => 'unpaid']);
            } elseif ($totalPaid < $invoice->total_amount) {
                $invoice->update(['status' => 'partial']);
            }

            // TRIGGER EVALUASI MASSAL (PENTING)
            // Karena uang ditarik kembali, hutang customer naik lagi.
            // Cek apakah ini menyebabkan Limit Jebol lagi? Jika ya, TAHAN ORDER.
            if ($so && $so->customer) {
                $so->customer->refreshOrderStatus();
            }
        });

        return back()->with('success', 'Pembayaran dihapus & Status Kredit Customer dievaluasi ulang.');
    }
}