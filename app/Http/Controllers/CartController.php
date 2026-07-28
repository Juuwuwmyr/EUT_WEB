<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{

    /**
     * GET /cart/sync
     * Returns all cart items for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $items = CartItem::where('user_id', auth()->id())
            ->orderBy('created_at')
            ->get()
            ->map(fn (CartItem $i) => $this->format($i));

        return response()->json(['items' => $items]);
    }

    /**
     * POST /cart/sync
     * Bulk-replace the entire cart with the provided items array.
     */
    public function bulkSync(Request $request): JsonResponse
    {
        $request->validate([
            'items'              => ['required', 'array'],
            'items.*.cart_key'   => ['required', 'string', 'max:255'],
            'items.*.menu_item_id' => ['required', 'integer'],
            'items.*.name'       => ['required', 'string', 'max:255'],
            'items.*.price'      => ['required', 'numeric', 'min:0'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $userId = auth()->id();

        CartItem::where('user_id', $userId)->delete();

        $saved = [];
        foreach ($request->items as $raw) {
            $item = CartItem::create([
                'user_id'      => $userId,
                'menu_item_id' => $raw['menu_item_id'],
                'cart_key'     => $raw['cart_key'],
                'item_name'    => $raw['name'],
                'image'        => $raw['image'] ?? null,
                'price'        => $raw['price'],
                'quantity'     => $raw['quantity'],
                'category'     => $raw['category'] ?? null,
                'modifiers'    => $raw['modifiers'] ?? null,
            ]);
            $saved[] = $this->format($item);
        }

        return response()->json(['success' => true, 'items' => $saved]);
    }

    /**
     * POST /cart/item
     * Upsert a single item (update quantity if cart_key exists, insert otherwise).
     */
    public function upsertItem(Request $request): JsonResponse
    {
        $request->validate([
            'cart_key'     => ['required', 'string', 'max:255'],
            'menu_item_id' => ['required', 'integer'],
            'name'         => ['required', 'string', 'max:255'],
            'price'        => ['required', 'numeric', 'min:0'],
            'quantity'     => ['required', 'integer', 'min:1'],
        ]);

        $userId = auth()->id();

        $item = CartItem::where('user_id', $userId)
            ->where('cart_key', $request->cart_key)
            ->first();

        if ($item) {
            $item->quantity = $request->quantity;
            $item->save();
        } else {
            $item = CartItem::create([
                'user_id'      => $userId,
                'menu_item_id' => $request->menu_item_id,
                'cart_key'     => $request->cart_key,
                'item_name'    => $request->name,
                'image'        => $request->image ?? null,
                'price'        => $request->price,
                'quantity'     => $request->quantity,
                'category'     => $request->category ?? null,
                'modifiers'    => $request->modifiers ?? null,
            ]);
        }

        return response()->json(['success' => true, 'item' => $this->format($item)]);
    }

    /**
     * PATCH /cart/item/{cartKey}
     * Update quantity only for a single cart item.
     */
    public function updateQty(Request $request, string $cartKey): JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        $key = urldecode($cartKey);

        CartItem::where('user_id', auth()->id())
            ->where('cart_key', $key)
            ->update(['quantity' => $request->quantity]);

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /cart/item/{cartKey}
     * Remove a single item by cart_key.
     */
    public function removeItem(string $cartKey): JsonResponse
    {
        $key = urldecode($cartKey);

        CartItem::where('user_id', auth()->id())
            ->where('cart_key', $key)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /cart
     * Clear the entire cart for the authenticated user.
     */
    public function clear(): JsonResponse
    {
        CartItem::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Format a CartItem model into the standard API shape.
     */
    private function format(CartItem $item): array
    {
        return [
            'id'           => $item->id,
            'cart_key'     => $item->cart_key,
            'menu_item_id' => $item->menu_item_id,
            'name'         => $item->item_name,
            'image'        => $item->image,
            'price'        => $item->price,
            'quantity'     => $item->quantity,
            'category'     => $item->category,
            'modifiers'    => $item->modifiers ?? [],
        ];
    }
}
