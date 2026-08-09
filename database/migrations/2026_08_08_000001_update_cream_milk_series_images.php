<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'Taro Cream Milk'      => '/images/menu/taro-cream-milk.webp',
            'Melon Cream Milk'     => '/images/menu/melon-cream-milk.webp',
            'Java Chip Vanilla'    => '/images/menu/java-chip-cream-milk.webp',
            'Mango Cheesecake'     => '/images/menu/mango-cheesecake-cream-milk.webp',
            'Okinawa Cream Milk'   => '/images/menu/okinawa-cream-milk.webp',
            'Strawberry Cream Milk'=> '/images/menu/strawberry-cream-milk.webp',
            'Matcha Cream Milk'    => '/images/menu/matcha-cream-milk.webp',
            'Chocolate Cream Milk' => '/images/menu/chocolate-cream-milk.webp',
            'Salted Caramel'       => '/images/menu/salted-caramel-cream-milk.webp',
            'Vanilla Cream Milk'   => '/images/menu/vanilla-cream-milk.webp',
            'Cookies & Cream'      => '/images/menu/cookies-cream-milk.webp',
            'Milk Chocolate'       => '/images/menu/milk-chocolate-cream-milk.webp',
        ];

        foreach ($updates as $name => $image) {
            DB::table('menu_items')->where('name', $name)->update(['image' => $image]);
        }
    }

    public function down(): void
    {
        // Revert to shared cream milk series image
        $names = [
            'Taro Cream Milk', 'Melon Cream Milk', 'Java Chip Vanilla',
            'Mango Cheesecake', 'Okinawa Cream Milk', 'Strawberry Cream Milk',
            'Matcha Cream Milk', 'Chocolate Cream Milk', 'Salted Caramel',
            'Vanilla Cream Milk', 'Cookies & Cream', 'Milk Chocolate',
        ];
        DB::table('menu_items')->whereIn('name', $names)
            ->update(['image' => '/images/menu/cream-milk-series.webp']);
    }
};
