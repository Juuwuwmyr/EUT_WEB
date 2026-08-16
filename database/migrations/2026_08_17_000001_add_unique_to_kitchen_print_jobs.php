<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove any duplicate unprinted rows before adding the unique constraint.
        // Keep the most recent row per (order_id, type) combination.
        $duplicates = DB::table('kitchen_print_jobs as a')
            ->join(
                DB::raw('(SELECT MAX(id) as max_id, order_id, type FROM kitchen_print_jobs GROUP BY order_id, type) as b'),
                function ($join) {
                    $join->on('a.order_id', '=', 'b.order_id')
                         ->on('a.type',     '=', 'b.type');
                }
            )
            ->where('a.id', '<', DB::raw('b.max_id'))
            ->pluck('a.id');

        if ($duplicates->isNotEmpty()) {
            DB::table('kitchen_print_jobs')->whereIn('id', $duplicates)->delete();
        }

        Schema::table('kitchen_print_jobs', function (Blueprint $table) {
            $table->unique(['order_id', 'type'], 'kitchen_print_jobs_order_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('kitchen_print_jobs', function (Blueprint $table) {
            $table->dropUnique('kitchen_print_jobs_order_type_unique');
        });
    }
};
