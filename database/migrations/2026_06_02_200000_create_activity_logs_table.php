<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who did it — nullable so system/unauthenticated events can still be logged
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // What happened
            $table->string('event');          // e.g. login, sale.created, sale.voided, product.updated
            $table->string('subject_type')->nullable(); // e.g. App\Models\Sale
            $table->unsignedBigInteger('subject_id')->nullable(); // e.g. 42

            // Human-readable description + optional JSON payload
            $table->text('description')->nullable();
            $table->json('context')->nullable(); // extra data (old/new values, void reason, etc.)

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();
            // No updated_at — logs are immutable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};