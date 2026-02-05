<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CashAccount;
use App\Models\CashTransaction;

class PurchasePaymentController extends Controller
{
    // 1. FORM PEMBAYARAN
    public function create($id)
    {
        // Load PO beserta history pembayarannya
        $purchase = Purchase::with(['supplier', 'payments.user'])->findOrFail($id);
        
        // Cek jika sudah lunas
        if ($purchase->payment_status == 'paid') {
            return back()->with('error', 'PO ini sudah lunas sepenuhnya!');
        }

        // AMBIL DATA AKUN KAS/BANK UNTUK DROPDOWN
        $accounts = CashAccount::orderBy('name')->get();

        return view('purchases.payment', compact('purchase', 'accounts'));
    }

    // 2. SIMPAN PEMBAYARAN & UPDATE KEUANGAN
    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'cash_account_id' => 'required|exists:cash_accounts,id', // Validasi Akun
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        $purchase = Purchase::findOrFail($request->purchase_id);
        $account = CashAccount::findOrFail($request->cash_account_id);

        // 1. Validasi: Jangan bayar lebih dari sisa hutang
        $remainingDebt = $purchase->total_amount - $purchase->amount_paid;
        
        // Toleransi pembulatan 1 rupiah agar tidak error karena floating point
        if ($request->amount > ($remainingDebt + 1)) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa hutang! Sisa: Rp ' . number_format($remainingDebt, 0));
        }

        // 2. Validasi: Cek Saldo Akun Cukup Gak?
        if ($account->balance < $request->amount) {
            return back()->with('error', 'Saldo akun ' . $account->name . ' tidak mencukupi! Saldo: Rp ' . number_format($account->balance, 0));
        }

        DB::transaction(function () use ($request, $purchase, $account) {
            // A. UPDATE PURCHASE (HUTANG) - Catat Payment
            PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'date' => $request->date,
                // Kita simpan nama akun sebagai payment_method agar history jelas
                'payment_method' => $account->name, 
                'notes' => $request->notes,
            ]);

            // Update Total Terbayar di PO
            $purchase->amount_paid += $request->amount;
            
            // Cek Status Lunas (Toleransi floating point 1 rupiah)
            if ($purchase->amount_paid >= ($purchase->total_amount - 1)) {
                $purchase->payment_status = 'paid';
            } else {
                $purchase->payment_status = 'partial';
            }
            $purchase->save();

            // B. UPDATE FINANCE (KAS KELUAR) - Catat Transaksi
            CashTransaction::create([
                'cash_account_id' => $account->id,
                'user_id' => Auth::id(),
                'type' => 'out', // Uang Keluar
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => 'Pembayaran Hutang PO: ' . $purchase->po_number . ' (' . $purchase->supplier->name . ')',
                'reference_id' => 'PO-' . $purchase->id,
            ]);

            // C. POTONG SALDO AKUN
            $account->decrement('balance', $request->amount);
        });

        return redirect()->route('purchases.index')->with('success', 'Pembayaran berhasil dicatat & Saldo ' . $account->name . ' telah dipotong.');
    }

    // 3. CETAK BUKTI PEMBAYARAN (VOUCHER)
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