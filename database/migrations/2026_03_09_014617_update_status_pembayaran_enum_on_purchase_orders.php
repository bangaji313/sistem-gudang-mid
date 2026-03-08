<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan opsi 'Void / Refund' ke kolom status_pembayaran.
     * SQLite: kolom bertipe TEXT, validasi di level aplikasi.
     * MySQL: ALTER TABLE ... MODIFY COLUMN untuk real ENUM constraint.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status_pembayaran ENUM('Lunas', 'DP', 'Tempo', 'Void / Refund') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status_pembayaran ENUM('Lunas', 'DP', 'Tempo') NOT NULL");
        }
    }
};
