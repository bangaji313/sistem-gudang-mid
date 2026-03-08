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
     * Menambahkan opsi 'Dibatalkan' ke kolom status_pesanan.
     * SQLite: kolom sudah bertipe TEXT, validasi dilakukan di level aplikasi.
     * MySQL: ALTER TABLE ... MODIFY COLUMN untuk real ENUM constraint.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status_pesanan ENUM('Selesai', 'Menunggu Kedatangan', 'Dibatalkan') DEFAULT 'Selesai'");
        }

        // Untuk SQLite: kolom sudah TEXT, validasi ENUM dilakukan di Controller & Model.
        // Migration ini berfungsi sebagai dokumentasi perubahan schema.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status_pesanan ENUM('Selesai', 'Menunggu Kedatangan') DEFAULT 'Selesai'");
        }
    }
};
