<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kitchen_print_jobs')) {
            return; // already created by a later migration — skip
        }

        Schema::create('kitchen_print_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('type')->default('ticket'); // 'ticket' or 'receipt'
            $table->boolean('printed')->default(false);
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['printed', 'created_at']);
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_print_jobs');
    }
};
