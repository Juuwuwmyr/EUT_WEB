<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend the enum to include 'addon'
        DB::statement("ALTER TABLE modifier_groups MODIFY COLUMN type ENUM('flavor','modifier','addon') NOT NULL DEFAULT 'modifier'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE modifier_groups MODIFY COLUMN type ENUM('flavor','modifier') NOT NULL DEFAULT 'modifier'");
    }
};
