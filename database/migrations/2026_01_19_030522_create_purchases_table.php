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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique(); // Contoh: PO-2026-0001
            
            // Relasi ke Supplier
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            
            // Siapa yang membuat PO ini? (User Admin/Staff)
            $table->foreignId('user_id')->constrained(); 
            
            $table->date('date');
            
            // Status PO: 
            // 'pending' = Baru draft / dikirim ke supplier
            // 'completed' = Barang sudah diterima (Stok Masuk)
            // 'canceled' = Batal
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
            
            $table->decimal('total_amount', 15, 2)->default(0); // Total Rupiah
            $table->text('notes')->nullable(); // Catatan tambahan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
