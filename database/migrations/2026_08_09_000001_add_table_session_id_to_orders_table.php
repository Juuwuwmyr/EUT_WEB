<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // UUID that groups all orders belonging to the same dine-in sitting.
            // A new UUID is stamped when a brand-new dine-in order is created;
            // add-on orders that merge into the same active order inherit this value.
            // This prevents old completed sessions from appearing on a new customer's receipt.
            $table->string('table_session_id', 36)->nullable()->after('table_number')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['table_session_id']);
            $table->dropColumn('table_session_id');
        });
    }
};
