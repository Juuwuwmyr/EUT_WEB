<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (null = guest / system / console)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 150)->nullable();  // snapshot at action time
            $table->string('user_role', 30)->nullable();   // snapshot at action time

            // What happened
            $table->string('action', 50);                  // created|updated|deleted|archived|restored|login|logout|settings_changed|status_changed etc.
            $table->string('description')->nullable();     // human-readable summary

            // Which record was affected
            $table->string('auditable_type', 100)->nullable();  // App\Models\Order
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('auditable_label', 191)->nullable(); // e.g. "EUT-00042" or "Burger Steak"

            // Before / after data
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url', 500)->nullable();

            $table->timestamps();

            // Indexes for the admin viewer queries
            $table->index(['auditable_type', 'auditable_id'], 'audit_morphable');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
