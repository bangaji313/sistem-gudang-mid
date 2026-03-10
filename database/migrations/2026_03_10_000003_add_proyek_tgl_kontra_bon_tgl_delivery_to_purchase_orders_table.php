<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('proyek')->nullable()->after('tgl_diterima');
            $table->date('tgl_kontra_bon')->nullable()->after('proyek');
            $table->date('tgl_delivery')->nullable()->after('tgl_kontra_bon');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['proyek', 'tgl_kontra_bon', 'tgl_delivery']);
        });
    }
};
