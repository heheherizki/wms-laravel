<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            // 1. Tambahkan kolom Hutang jika belum ada
            if (!Schema::hasColumn('purchases', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('purchases', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
            }
            
            // 2. Kita tidak perlu 'grand_total' jika sudah ada 'total_amount'
            // Kita akan pakai 'total_amount' saja di Controller agar tidak error.
        });
    }

    public function down()
    {
        // Kosongkan saja agar aman
    }
};