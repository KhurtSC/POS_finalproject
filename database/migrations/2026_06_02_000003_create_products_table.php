<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique()->nullable();   // barcode / item code
            $table->text('description')->nullable();
            $table->string('image')->nullable();           // stored path
            $table->decimal('price', 10, 2);               // selling price
            $table->decimal('cost', 10, 2)->default(0);    // purchase / cost price
            $table->unsignedInteger('stock')->default(0);  // current stock quantity
            $table->unsignedInteger('low_stock_threshold')->default(5); // alert level
            $table->boolean('is_available')->default(true); // can be ordered today
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};