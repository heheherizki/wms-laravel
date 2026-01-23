<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel products
            // constrained() artinya wajib ada id product yg valid
            // cascadeOnDelete() artinya jika produk dihapus permanen, history juga hilang (opsional)
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            
            $table->enum('type', ['in', 'out']); // Tipe: Masuk / Keluar
            $table->integer('quantity'); // Jumlah
            
            $table->string('reference')->nullable(); // Supplier / No PO / Customer
            $table->text('notes')->nullable(); // Catatan tambahan
            
            $table->foreignId('user_id')->nullable(); // Siapa adminnya (nanti kita pakai)
            
            $table->timestamps(); // Mencatat kapan dibuat (created_at)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};