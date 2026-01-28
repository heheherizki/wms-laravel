<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'payment_terms',
        'credit_limit',
        'authorized_until',
    ];

    protected $casts = [
        'authorized_until' => 'datetime', // <--- Wajib agar fungsi waktu jalan
    ];

    // Helper: Cek apakah customer sedang dalam masa "Bebas Hold" (Sakti)
    public function isAuthorized()
    {
        return $this->authorized_until && now()->lt($this->authorized_until);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    // Helper: Cek apakah ada invoice yang sudah jatuh tempo (Overdue)
    public function hasOverdueInvoices()
    {
        // Cari Invoice milik customer ini
        return Invoice::whereHas('salesOrder', function($q) {
            $q->where('customer_id', $this->id);
        })
        ->whereIn('status', ['unpaid', 'partial']) // Yang belum lunas
        ->whereDate('due_date', '<', now()) // Yang tanggal jatuh temponya sudah lewat hari ini
        ->exists(); // Return true jika ada walau cuma 1
    }

    // Helper: Hitung Total Hutang Saat Ini (Untuk Risk Management)
    public function getCurrentDebtAttribute()
    {
        // Ambil invoice unpaid/partial milik customer ini
        return Invoice::whereHas('salesOrder', function($q) {
            $q->where('customer_id', $this->id);
        })->whereIn('status', ['unpaid', 'partial'])->get()->sum('remaining_balance');
    }

    public function refreshOrderStatus()
    {
        // 1. Cek Status "Surat Sakti" (Unlock Admin)
        $isAuthorized = $this->authorized_until && now()->lt($this->authorized_until);

        if ($isAuthorized) {
            // SKENARIO A: CUSTOMER SEDANG DI-UNLOCK ADMIN
            // Lepas SEMUA order yang tertahan, tanpa peduli limit/overdue
            $this->salesOrders()
                 ->where('status', 'on_hold')
                 ->update(['status' => 'pending']);
            
            return; // Selesai, tidak perlu cek limit
        }

        // 2. Cek Kesehatan Customer Detik Ini
        $hasOverdue = $this->hasOverdueInvoices();
        
        // Hitung total hutang real-time (Invoice Belum Lunas + Order Pending/Hold yang belum jadi invoice)
        // Note: Kita ambil SO pending/hold untuk dihitung sebagai eksposur
        $pendingOrdersValue = $this->salesOrders()
                                   ->whereIn('status', ['pending', 'on_hold'])
                                   ->sum('grand_total');
                                   
        // Total Exposure = Hutang Invoice (Real) + Order Gantung
        // Catatan: current_debt biasanya hanya invoice. Kita perlu logika hutang yang sesuai sistem Anda.
        // Jika current_debt di sistem Anda sudah termasuk SO, gunakan itu. 
        // Jika current_debt hanya invoice, maka tambahkan $pendingOrdersValue.
        // Asumsi: current_debt = Invoice Unpaid.
        
        // Cek Limit
        $isOverLimit = $this->credit_limit > 0 && 
                       (($this->current_debt + $pendingOrdersValue) > $this->credit_limit);


        // 3. EKSEKUSI MASSAL (BULK ACTION)

        if ($hasOverdue) {
            // KASUS: ADA TAGIHAN MACET
            // HUKUMAN: Tahan SEMUA order yang masih pending
            $this->salesOrders()
                 ->where('status', 'pending')
                 ->where('payment_status', '!=', 'paid')
                 ->update(['status' => 'on_hold']);
                 
        } elseif ($isOverLimit) {
            // KASUS: LIMIT JEBOL
            // HUKUMAN: Tahan SEMUA order pending
            // (Opsional: Anda bisa buat logika canggih untuk menahan sebagian saja, 
            // tapi untuk keamanan, biasanya sistem menahan semua sampai limit dinaikkan/dibayar)
            $this->salesOrders()
                 ->where('status', 'pending')
                 ->where('payment_status', '!=', 'paid')
                 ->update(['status' => 'on_hold']);
                 
        } else {
            // KASUS: SEHAT (Tidak Overdue & Tidak Overlimit)
            // HADIAH: Lepas SEMUA order yang tertahan
            $this->salesOrders()
                 ->where('status', 'on_hold')
                 ->update(['status' => 'pending']);
        }
    }
}