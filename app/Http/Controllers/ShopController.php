<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::active()->orderBy('sort_order')->get();
        $menuItems  = \App\Models\MenuItem::with('category')->active()
                        ->orderBy('category_id')->orderBy('sort_order')->get();

        return view('shop.index', compact('categories', 'menuItems'));
    }

    public function product($id)
    {
        $item = \App\Models\MenuItem::with([
            'category',
            'modifierGroups' => function($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            },
            'modifierGroups.activeOptions' => function($q) {
                $q->orderBy('sort_order');
            },
        ])->active()->find((int) $id);

        if (!$item) {
            abort(404);
        }

        $item = $item->toArray();

        // Separate addon groups so the view can handle them distinctly
        $item['addon_groups']    = array_values(array_filter($item['modifier_groups'] ?? [], fn($g) => $g['type'] === 'addon'));
        $item['modifier_groups'] = array_values(array_filter($item['modifier_groups'] ?? [], fn($g) => $g['type'] !== 'addon'));

        return view('shop.product', compact('item'));
    }

    public function cart()
    {
        return view('shop.cart');
    }

    public function checkout()
    {
        return view('shop.checkout');
    }

    public function tracking()
    {
        return view('shop.tracking');
    }

    public function profile()
    {
        return view('shop.profile');
    }
}
