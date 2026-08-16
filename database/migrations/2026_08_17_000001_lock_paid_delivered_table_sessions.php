<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Close out table sessions for orders that were already paid but never locked.
     * Prevents old paid bills from appearing on new customer prints at the same table.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'ordering_locked') || ! Schema::hasColumn('orders', 'payment_status')) {
            return;
        }

        DB::table('orders')
            ->where('order_type', 'dine_in')
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->where('ordering_locked', false)
            ->update(['ordering_locked' => true]);
    }

    public function down(): void
    {
        // Non-reversible data fix — leave paid sessions locked.
    }
};
