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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Admin yg input
            $table->string('payment_number')->unique(); // No Kwitansi (PAY-2026...)
            $table->date('date');
            $table->decimal('amount', 15, 2); // Nominal bayar
            $table->string('payment_method'); // Cash, Transfer BCA, Cek, dll
            $table->string('note')->nullable(); // Catatan / Bukti Transfer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
