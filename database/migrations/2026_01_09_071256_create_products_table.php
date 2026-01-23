<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // 1. Identitas Unik
            $table->string('sku')->unique(); // Contoh: L-DL-5W-WHT (Lampu Downlight 5W White)
            
            // 2. Detail Produk Elektronik
            $table->string('name'); // Contoh: LED Downlight Eco
            $table->string('brand'); // PENTING: Merk (Tiger, Lion, Stark, dll)
            $table->string('category'); // Kategori: Lampu, Senter, Raket Nyamuk
            
            // Spesifikasi Teknis (Penting untuk Elektronik/Lampu)
            $table->string('watt')->nullable(); // Contoh: 5 Watt, 12 Watt
            $table->string('color')->nullable(); // Contoh: 6500K (Putih), 3000K (Kuning/Warm)
            
            // 3. Stok & Multi-Unit (Logika Gudang)
            $table->integer('stock')->default(0); // Stok Base Unit (Pcs)
            $table->integer('min_stock')->default(50); // Alert jika stok < 50 Pcs
            
            $table->string('unit')->default('Pcs'); // Satuan terkecil
            $table->string('pack_unit')->nullable(); // Satuan besar: "Dus" / "Koli" / "Master Box"
            $table->integer('pack_quantity')->nullable(); // Isi per dus (misal: 40 pcs)
            
            // 4. Manajemen Gudang
            $table->string('rack_location')->nullable(); // Lokasi Rak: G-01-A
            
            // Gambar produk (Penting agar orang gudang bisa cocokin visual)
            $table->string('image')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Agar data aman
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};