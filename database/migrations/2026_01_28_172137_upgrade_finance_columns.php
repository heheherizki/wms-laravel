<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Update Tabel Suppliers
        // Kita cek dulu apakah kolom term_days sudah ada?
        if (!Schema::hasColumn('suppliers', 'term_days')) {
            Schema::table('suppliers', function (Blueprint $table) {
                // Hapus ->after(...), biarkan default
                $table->integer('term_days')->default(0)->comment('Jatuh tempo (hari)');
            });
        }

        // 2. Update Tabel Purchases
        if (!Schema::hasColumn('purchases', 'payment_status')) {
            Schema::table('purchases', function (Blueprint $table) {
                // Hapus ->after(...)
                $table->string('payment_status')->default('unpaid');
                $table->decimal('amount_paid', 15, 2)->default(0);
            });
        }
        
        // 3. Update Tabel Products (INI YANG TADI ERROR)
        if (!Schema::hasColumn('products', 'purchase_price')) {
            Schema::table('products', function (Blueprint $table) {
                // Hapus ->after('price') karena kolom price tidak ditemukan
                $table->decimal('purchase_price', 15, 2)->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        
    }
};
