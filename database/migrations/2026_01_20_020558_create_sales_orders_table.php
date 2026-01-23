<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number')->unique(); // Contoh: SO-2026-001
            
            // Relasi ke Customer & User Sales
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); 
            
            $table->date('date');
            
            // Status Pengiriman Barang
            // pending = Pesanan dibuat (Barang belum dikirim)
            // shipped = Barang sudah keluar gudang (Stok berkurang)
            // canceled = Batal
            $table->enum('status', ['pending', 'shipped', 'canceled'])->default('pending');

            // Status Pembayaran (Untuk Keuangan nanti)
            $table->enum('payment_status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable(); // Catatan pengiriman
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
