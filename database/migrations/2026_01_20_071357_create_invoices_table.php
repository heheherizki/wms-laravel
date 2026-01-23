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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // INV/2026/001
            
            // Relasi ke Shipment (1 Invoice = 1 Surat Jalan)
            $table->foreignId('shipment_id')->constrained()->unique(); 
            
            // Relasi ke SO (Untuk referensi)
            $table->foreignId('sales_order_id')->constrained();
            
            $table->date('date');
            $table->date('due_date'); // Jatuh Tempo
            $table->decimal('total_amount', 15, 2);
            
            // Status Pembayaran
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
