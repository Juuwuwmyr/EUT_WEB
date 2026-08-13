<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add ordering_locked flag to orders.
     *
     * When an admin marks a dine-in table session as "Done Ordering" /
     * locked, every order in that session gets ordering_locked = true.
     * The OrderController will refuse to merge any subsequent order into
     * a locked session and will instead create a fresh session — ensuring
     * a new customer at the same table is never mixed with a previous one.
     */
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'ordering_locked')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('ordering_locked')
                  ->default(false)
                  ->after('table_session_id')
                  ->comment('True = admin closed ordering for this session; new orders start a fresh session');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('orders', 'ordering_locked')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('ordering_locked');
        });
    }
};
