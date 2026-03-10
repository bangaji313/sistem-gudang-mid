<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_detail_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_detail_id')
                  ->constrained('purchase_order_details')
                  ->cascadeOnDelete();
            $table->integer('old_qty');
            $table->integer('new_qty');
            $table->decimal('old_price', 15, 2);
            $table->decimal('new_price', 15, 2);
            $table->string('keterangan')->nullable();
            $table->foreignId('revised_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_detail_revisions');
    }
};
