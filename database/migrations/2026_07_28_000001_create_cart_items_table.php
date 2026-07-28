<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('menu_item_id');
            $table->string('cart_key')->comment('composite key e.g. 5_12-14 for dedup');
            $table->string('item_name');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->string('category')->nullable();
            $table->json('modifiers')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'cart_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
