<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Menambahkan kolom credit_limit (angka desimal besar)
            // Default 0 artinya Unlimited/Belum diset
            $table->decimal('credit_limit', 15, 2)->default(0)->after('email'); 
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('credit_limit');
        });
    }
};