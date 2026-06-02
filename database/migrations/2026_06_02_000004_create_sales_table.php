<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();          // e.g. TXN-20260602-0001

            // Cashier who processed the sale
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // Amounts
            $table->decimal('subtotal', 10, 2);             // sum of item totals before discount
            $table->decimal('discount_amount', 10, 2)->default(0); // flat or computed discount
            $table->decimal('discount_percent', 5, 2)->default(0); // % applied (0 if flat)
            $table->decimal('total_amount', 10, 2);         // subtotal - discount

            // Payment
            $table->enum('payment_method', ['cash', 'card', 'gcash', 'other'])->default('cash');
            $table->decimal('amount_tendered', 10, 2)->default(0); // cash handed by customer
            $table->decimal('change_amount', 10, 2)->default(0);   // change returned

            // Status
            $table->enum('status', ['completed', 'voided'])->default('completed');
            $table->text('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};