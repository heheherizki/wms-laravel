<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Header Retur Pembelian
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            // Relasi ke Purchase Order (PO) yang mana?
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Siapa yang input
            
            $table->date('date');
            $table->string('reason')->nullable(); // Alasan retur (Rusak/Salah Kirim)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 2. Detail Barang yang Diretur
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            
            $table->integer('quantity');
            $table->text('notes')->nullable(); // Catatan per item
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_return_details');
        Schema::dropIfExists('purchase_returns');
    }
};