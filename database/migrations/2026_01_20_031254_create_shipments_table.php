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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // SJ-001
            $table->foreignId('sales_order_id')->constrained(); // Link ke SO
            $table->foreignId('user_id')->constrained(); // Siapa yang kirim
            $table->date('date');
            $table->text('notes')->nullable(); // Nama Supir / Plat No
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
