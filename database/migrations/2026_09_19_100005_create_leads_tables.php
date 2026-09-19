<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every form submission on the site (quote, callback, sample, …).
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();                  // Lead::TYPES
            $table->string('status', 20)->default('new')->index(); // Lead::STATUSES
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('company')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('message')->nullable();
            $table->json('payload')->nullable(); // type-specific fields, e.g. RFQ specs
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->json('utm')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('lead_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('path');          // on the private `local` disk
            $table->string('original_name');
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_files');
        Schema::dropIfExists('leads');
    }
};
