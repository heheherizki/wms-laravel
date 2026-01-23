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
        Schema::table('products', function (Blueprint $table) {
            // Tipe Barang: 
            // 'finished_good' = Barang Jadi (Dijual)
            // 'sparepart' = Suku Cadang (Dipakai mesin)
            // 'raw_material' = Bahan Baku (Opsional, buat nanti)
            $table->enum('type', ['finished_good', 'sparepart', 'raw_material'])
                  ->default('finished_good')
                  ->after('name'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
