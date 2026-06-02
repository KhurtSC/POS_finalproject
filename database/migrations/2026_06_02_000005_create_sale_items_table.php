<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();

            // Snapshot the product data at time of sale so historical records
            // stay accurate even if the product is later edited or deleted.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');                // snapshot
            $table->string('product_sku')->nullable();     // snapshot
            $table->decimal('unit_price', 10, 2);          // price at time of sale
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);            // unit_price × quantity

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};