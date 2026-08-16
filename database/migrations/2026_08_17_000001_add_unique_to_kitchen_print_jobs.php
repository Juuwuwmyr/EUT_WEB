<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kitchen_print_jobs')) {
            // Server never ran the original create migration — create the table
            // with the unique constraint already baked in.
            Schema::create('kitchen_print_jobs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('type')->default('ticket'); // 'ticket' or 'receipt'
                $table->boolean('printed')->default(false);
                $table->timestamp('printed_at')->nullable();
                $table->timestamps();

                $table->index(['printed', 'created_at']);
                $table->index('order_id');
                $table->unique(['order_id', 'type'], 'kitchen_print_jobs_order_type_unique');
            });

            return;
        }

        // Table already exists (local dev) — clean up any duplicate rows first,
        // then add the unique constraint if it isn't there yet.

        // Remove duplicates: keep only the most recent row per (order_id, type).
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

        // Add the unique constraint only if it doesn't already exist.
        $sm      = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('kitchen_print_jobs');

        if (!array_key_exists('kitchen_print_jobs_order_type_unique', $indexes)) {
            Schema::table('kitchen_print_jobs', function (Blueprint $table) {
                $table->unique(['order_id', 'type'], 'kitchen_print_jobs_order_type_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kitchen_print_jobs')) {
            Schema::table('kitchen_print_jobs', function (Blueprint $table) {
                $table->dropUnique('kitchen_print_jobs_order_type_unique');
            });
        }
    }
};
