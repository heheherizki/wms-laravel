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
        // 1. Hapus dari sales_orders
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('authorized_until');
        });

        // 2. Tambah ke customers
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('authorized_until')->nullable()->after('credit_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kebalikannya jika rollback
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('authorized_until');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->timestamp('authorized_until')->nullable();
        });
    }
};
