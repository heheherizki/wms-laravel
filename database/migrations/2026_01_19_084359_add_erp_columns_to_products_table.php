<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            
            // 1. Cek & Tambah Kolom TIPE
            if (!Schema::hasColumn('products', 'type')) {
                $table->enum('type', ['finished_good', 'sparepart', 'raw_material'])
                      ->default('finished_good')
                      ->after('name'); 
            }

            // 2. Cek & Tambah Kolom HARGA BELI
            if (!Schema::hasColumn('products', 'buy_price')) {
                $table->decimal('buy_price', 15, 2)->default(0)->after('stock');
            }

            // 3. Cek & Tambah Kolom HARGA JUAL
            if (!Schema::hasColumn('products', 'sell_price')) {
                $table->decimal('sell_price', 15, 2)->default(0)->after('buy_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom jika rollback
            if (Schema::hasColumn('products', 'type')) $table->dropColumn('type');
            if (Schema::hasColumn('products', 'buy_price')) $table->dropColumn('buy_price');
            if (Schema::hasColumn('products', 'sell_price')) $table->dropColumn('sell_price');
        });
    }
};