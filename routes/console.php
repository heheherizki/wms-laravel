<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Customer;

Schedule::call(function () {
    
    // STRATEGI BARU: DELEGASI KE MODEL (CENTRALIZED LOGIC)
    
    // 1. Ambil hanya Customer yang memiliki Order Aktif (Pending / On Hold)
    // Kita tidak perlu mengecek customer yang sedang tidak belanja (tidak punya order gantung)
    $activeCustomers = Customer::whereHas('salesOrders', function($q) {
        $q->whereIn('status', ['pending', 'on_hold'])
          ->where('payment_status', '!=', 'paid');
    })->get();

    // 2. Suruh setiap Customer untuk "Check-Up" Kesehatan
    foreach ($activeCustomers as $customer) {
        // Panggil fungsi "Big Switch" yang sama persis dengan yang dipakai di Controller.
        // Fungsi ini otomatis akan:
        // - Cek Timer Unlock (Apakah sudah expired?)
        // - Cek Overdue Invoice (Apakah baru saja jatuh tempo menit ini?)
        // - Cek Limit (Apakah total hutang + order pending melebihi limit?)
        // - Lalu Update status SO secara massal.
        $customer->refreshOrderStatus();
    }

})->everyMinute();