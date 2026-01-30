<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchasePaymentController extends Controller
{
    public function create($id)
    {
        // Load PO beserta history pembayarannya
        $purchase = Purchase::with(['supplier', 'payments.user'])->findOrFail($id);
        
        if ($purchase->payment_status == 'paid') {
            return back()->with('error', 'PO ini sudah lunas!');
        }

        return view('purchases.payment', compact('purchase'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string', // Kolom baru (Opsional)
        ]);

        $po = Purchase::findOrFail($request->purchase_id);
        $sisaHutang = $po->total_amount - $po->amount_paid;

        if ($request->amount > $sisaHutang) {
            return back()->with('error', 'Jumlah bayar melebihi sisa hutang!');
        }

        DB::transaction(function () use ($request, $po) {
            // 1. SIMPAN HISTORY PEMBAYARAN (TRACKING)
            PurchasePayment::create([
                'purchase_id' => $po->id,
                'user_id' => Auth::id(), // Mencatat siapa yang bayar
                'date' => $request->date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'notes' => 'Pembayaran Hutang PO ' . $po->po_number,
            ]);

            // 2. UPDATE TOTAL DI PO (Induk)
            $po->amount_paid += $request->amount;

            if ($po->amount_paid >= ($po->total_amount - 1)) {
                $po->payment_status = 'paid';
            } else {
                $po->payment_status = 'partial';
            }
            $po->save();
        });

        return redirect()->route('purchases.show', $po->id)
            ->with('success', 'Pembayaran berhasil dicatat & masuk history.');
    }

    public function print($id)
    {
        $payment = PurchasePayment::with(['purchase.supplier', 'user'])->findOrFail($id);
        
        // Konversi angka ke kalimat terbilang (Fitur Premium ala ERP)
        $terbilang = $this->terbilang($payment->amount) . ' Rupiah';

        $pdf = Pdf::loadView('purchases.payment_pdf', compact('payment', 'terbilang'));
        
        // Ukuran kertas A5 Landscape (Setengah A4) biasanya cukup untuk Voucher
        $pdf->setPaper('a5', 'landscape');
        
        return $pdf->stream('Voucher-' . $payment->purchase->po_number . '-' . $payment->id . '.pdf');
    }

    // Fungsi Helper: Mengubah Angka jadi Kalimat (Indonesian)
    private function terbilang($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->terbilang($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai/10)." Puluh". $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai/100) . " Ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai/1000) . " Ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai/1000000) . " Juta" . $this->terbilang($nilai % 1000000);
        }
        return $temp;
    }
}