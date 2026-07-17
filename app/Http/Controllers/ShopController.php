<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop.index');
    }

    public function product($id)
    {
        $menuItems = \App\Models\MenuItem::getAllMenuItems();
        $item = collect($menuItems)->firstWhere('id', $id);

        if (!$item) {
            abort(404);
        }

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
}
