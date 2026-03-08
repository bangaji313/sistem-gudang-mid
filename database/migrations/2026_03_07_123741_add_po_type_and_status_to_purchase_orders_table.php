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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('tipe_po', ['Ready Stock', 'Pre-Order'])->default('Ready Stock')->after('tanggal_po');
            $table->date('estimasi_sampai')->nullable()->after('tipe_po');
            $table->enum('status_pesanan', ['Selesai', 'Menunggu Kedatangan'])->default('Selesai')->after('estimasi_sampai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['tipe_po', 'estimasi_sampai', 'status_pesanan']);
        });
    }
};
