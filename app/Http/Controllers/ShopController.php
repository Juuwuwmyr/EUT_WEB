<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $categories     = \App\Models\Category::active()->orderBy('sort_order')->get();
        $menuItems      = \App\Models\MenuItem::with('category')->active()
                            ->orderBy('category_id')->orderBy('sort_order')->get();
        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);
        $isOpen         = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;

        return view('shop.index', compact('categories', 'menuItems', 'isOpen', 'isOpenDelivery', 'isOpenPickup', 'isOpenDineIn'));
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
        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);
        $isOpen         = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;

        $upsellItems = \App\Models\MenuItem::where('is_archived', false)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->inRandomOrder()
            ->limit(8)
            ->get();
        return view('shop.cart', compact('isOpen', 'isOpenDelivery', 'isOpenPickup', 'isOpenDineIn', 'upsellItems'));
    }

    public function checkout()
    {
        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);
        $isOpen         = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;

        return view('shop.checkout', compact('isOpen', 'isOpenDelivery', 'isOpenPickup', 'isOpenDineIn'));
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
