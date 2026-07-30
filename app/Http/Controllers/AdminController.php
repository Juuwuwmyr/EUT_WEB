<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ════════════════════════════════════════════════════════
    // DASHBOARD
    // ════════════════════════════════════════════════════════

    public function dashboard()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_customers'  => User::where('role', 'user')->count(),
            'admin_users'      => User::where('role', 'admin')->count(),
            'chef_users'       => User::where('role', 'chef')->count(),
            'rider_users'      => User::where('role', 'rider')->count(),
            'active_riders'    => \App\Models\Rider::where('is_available', true)->count(),
            'total_items'      => MenuItem::active()->count(),
            'total_categories' => Category::active()->count(),
            'featured_items'   => MenuItem::featured()->count(),
            'total_orders'     => \App\Models\Order::count(),
            'today_orders'     => \App\Models\Order::whereDate('created_at', today())->count(),
            'pending_orders'   => \App\Models\Order::where('status', 'pending')->count(),
            'today_revenue'    => \App\Models\Order::where('status', 'delivered')->whereDate('delivered_at', today())->sum('total'),
            'total_revenue'    => \App\Models\Order::where('status', 'delivered')->sum('total'),
        ];

        $recent_users = User::latest()->take(5)->get();

        $categories = Category::active()->withCount(['activeMenuItems'])->orderBy('sort_order')->get()->map(fn($c) => [
            'name'  => $c->name,
            'count' => $c->active_menu_items_count,
            'hex'   => $c->color,
            'icon'  => $c->icon,
            'slug'  => $c->slug,
        ])->toArray();

        // Top-selling menu items (by total quantity sold from delivered orders)
        $topItems = \App\Models\OrderItem::select('menu_item_id', 'item_name',
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'),
                \Illuminate\Support\Facades\DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->groupBy('menu_item_id', 'item_name')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get()
            ->map(fn($i) => [
                'name'          => $i->item_name,
                'total_sold'    => (int) $i->total_sold,
                'total_revenue' => (float) $i->total_revenue,
                'image'         => optional(\App\Models\MenuItem::find($i->menu_item_id))->image ?? '/images/hero-burger.webp',
                'category'      => optional(\App\Models\MenuItem::with('category')->find($i->menu_item_id))->category?->name ?? '—',
                'category_color'=> optional(\App\Models\MenuItem::with('category')->find($i->menu_item_id))->category?->color ?? '#6b7280',
            ])->toArray();

        return view('admin.dashboard', compact('stats', 'recent_users', 'categories', 'topItems'));
    }

    // ════════════════════════════════════════════════════════
    // USERS
    // ════════════════════════════════════════════════════════

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => ['required', Rule::in(['admin', 'user', 'chef', 'rider'])],
            'phone'        => 'required_if:role,rider|nullable|string|max:20',
            'plate_number' => 'nullable|string|max:30',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'provider' => 'email',
        ]);

        // If rider, also create the Rider profile record
        if ($request->role === 'rider') {
            \App\Models\Rider::create([
                'user_id'      => $user->id,
                'phone'        => $request->phone,
                'plate_number' => $request->plate_number,
                'is_available' => false,
            ]);
        }

        return back()->with('success', "User \"{$request->name}\" created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'role'  => ['required', Rule::in(['admin', 'user', 'chef', 'rider'])],
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $data = ['name' => $request->name, 'email' => $request->email, 'role' => $request->role];
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', "User \"{$user->name}\" updated successfully.");
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => ['required', Rule::in(['admin', 'user', 'chef'])]]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }
        $user->update(['role' => $request->role]);
        return back()->with('success', "Role updated to {$request->role}.");
    }

    public function archiveUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot archive your own account.');
        }
        // Uses soft "role" disable — sets role to 'archived' to block login
        $user->update(['role' => 'archived']);
        return back()->with('success', "User \"{$user->name}\" archived.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "User \"{$name}\" deleted.");
    }

    // ════════════════════════════════════════════════════════
    // CATEGORIES
    // ════════════════════════════════════════════════════════

    public function categories()
    {
        $categories = Category::withCount(['activeMenuItems'])->orderBy('sort_order')->get();

        // Data-mining summary stats
        $totalItems      = \App\Models\MenuItem::active()->count();
        $totalCats       = $categories->where('is_archived', false)->count();
        $archivedCats    = $categories->where('is_archived', true)->count();

        // Best-selling category (most items sold from delivered orders)
        $bestCat = \App\Models\OrderItem::select('menu_item_id',
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->groupBy('menu_item_id')
            ->get()
            ->groupBy(fn($row) => optional(\App\Models\MenuItem::find($row->menu_item_id))->category?->name ?? 'Unknown')
            ->map(fn($rows) => $rows->sum('qty'))
            ->sortDesc()
            ->first() ? null : null; // compute label below

        $catSales = \App\Models\OrderItem::select('menu_item_id',
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->groupBy('menu_item_id')
            ->get()
            ->groupBy(fn($row) => optional(\App\Models\MenuItem::with('category')->find($row->menu_item_id))->category?->name ?? 'Unknown')
            ->map(fn($rows) => $rows->sum('qty'))
            ->sortDesc();

        $bestCatName  = $catSales->keys()->first() ?? '—';
        $bestCatCount = $catSales->first() ?? 0;

        $menuStats = [
            ['label'=>'Total Categories', 'value'=>$totalCats,    'sub'=>$archivedCats.' archived',      'icon'=>'layout-grid',   'color'=>'#10b981','bg'=>'rgba(16,185,129,.12)'],
            ['label'=>'Total Items',      'value'=>$totalItems,   'sub'=>'Active menu items',             'icon'=>'utensils',      'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.12)'],
            ['label'=>'Avg Items/Cat',    'value'=>$totalCats > 0 ? round($totalItems/$totalCats,1) : 0,'sub'=>'Items per category','icon'=>'bar-chart-2','color'=>'#6366f1','bg'=>'rgba(99,102,241,.12)'],
            ['label'=>'Best Selling Cat', 'value'=>$bestCatName,  'sub'=>number_format($bestCatCount).' units sold', 'icon'=>'trophy','color'=>'#facc15','bg'=>'rgba(250,204,21,.12)'],
            ['label'=>'Active Rate',      'value'=>$totalCats+$archivedCats > 0 ? round($totalCats/($totalCats+$archivedCats)*100).'%' : '—', 'sub'=>'Categories active','icon'=>'activity','color'=>'#22c55e','bg'=>'rgba(34,197,94,.12)'],
        ];

        return view('admin.categories', compact('categories', 'menuStats'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:80',
            'icon'        => 'required|string|max:50',
            'color'       => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        $base = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        Category::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'icon'        => $request->icon,
            'color'       => $request->color,
            'description' => $request->description,
            'sort_order'  => Category::max('sort_order') + 1,
        ]);

        return back()->with('success', "Category \"{$request->name}\" created.");
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:80',
            'icon'        => 'required|string|max:50',
            'color'       => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        $category->update([
            'name'        => $request->name,
            'icon'        => $request->icon,
            'color'       => $request->color,
            'description' => $request->description,
        ]);

        return back()->with('success', "Category \"{$category->name}\" updated.");
    }

    public function archiveCategory(Category $category)
    {
        $category->update(['is_archived' => ! $category->is_archived]);
        $state = $category->is_archived ? 'archived' : 'restored';
        return back()->with('success', "Category \"{$category->name}\" {$state}.");
    }

    public function deleteCategory(Category $category)
    {
        if ($category->menuItems()->exists()) {
            return back()->with('error', "Cannot delete \"{$category->name}\" — it still has menu items. Archive it or move items first.");
        }
        $name = $category->name;
        $category->delete();
        return back()->with('success', "Category \"{$name}\" deleted.");
    }

    // ════════════════════════════════════════════════════════
    // MENU ITEMS
    // ════════════════════════════════════════════════════════

    public function menuItems(Request $request)
    {
        $query = MenuItem::with(['category', 'modifierGroups.options']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('description','like',"%$s%"));
        }
        if ($request->boolean('archived')) {
            $query->archived();
        } else {
            $query->active();
        }

        $items      = $query->orderBy('category_id')->orderBy('sort_order')->get();
        $categories = Category::active()->orderBy('sort_order')->get();

        // Data-mining summary stats for menu items page
        $totalActive   = \App\Models\MenuItem::active()->count();
        $totalFeatured = \App\Models\MenuItem::featured()->count();
        $totalArchived = \App\Models\MenuItem::archived()->count();
        $avgPrice      = \App\Models\MenuItem::active()->avg('price') ?? 0;

        // Most popular item
        $topItem = \App\Models\OrderItem::select('item_name',
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
            ->groupBy('item_name')
            ->orderByDesc('qty')
            ->first();

        $menuItemStats = [
            ['label'=>'Active Items',   'value'=>$totalActive,                         'sub'=>$totalArchived.' archived',          'icon'=>'utensils',     'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.12)'],
            ['label'=>'Featured',       'value'=>$totalFeatured,                       'sub'=>'Highlighted on menu',               'icon'=>'star',         'color'=>'#facc15','bg'=>'rgba(250,204,21,.12)'],
            ['label'=>'Avg Price',      'value'=>'₱'.number_format($avgPrice,0),       'sub'=>'Per active item',                   'icon'=>'tag',          'color'=>'#10b981','bg'=>'rgba(16,185,129,.12)'],
            ['label'=>'Best Seller',    'value'=>$topItem?->item_name ?? '—',          'sub'=>number_format($topItem?->qty ?? 0).' units sold','icon'=>'trophy','color'=>'#a78bfa','bg'=>'rgba(167,139,250,.12)'],
            ['label'=>'Categories',     'value'=>$categories->count(),                 'sub'=>'Active categories',                 'icon'=>'layout-grid',  'color'=>'#22c55e','bg'=>'rgba(34,197,94,.12)'],
        ];

        return view('admin.menu-items', compact('items', 'categories', 'menuItemStats'));
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'name'                                  => 'required|string|max:120',
            'category_id'                           => 'required|exists:categories,id',
            'price'                                 => 'required|numeric|min:0',
            'description'                           => 'nullable|string|max:400',
            'image_file'                            => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:4096',
            'featured'                              => 'nullable|boolean',
            'groups'                                => 'nullable|array',
            'groups.*.type'                         => 'required_with:groups|in:flavor,modifier,addon',
            'groups.*.name'                         => 'required_with:groups|string|max:80',
            'groups.*.required'                     => 'nullable|boolean',
            'groups.*.is_active'                    => 'nullable|boolean',
            'groups.*.options'                      => 'nullable|array',
            'groups.*.options.*.name'               => 'required_with:groups.*.options|string|max:80',
            'groups.*.options.*.price_type'         => 'required_with:groups.*.options|in:none,add,replace',
            'groups.*.options.*.price_adjustment'   => 'nullable|numeric|min:0',
            'groups.*.options.*.is_default'         => 'nullable|boolean',
            'groups.*.options.*.is_active'          => 'nullable|boolean',
        ]);

        // Handle image upload
        $imagePath = '/images/hero-burger.webp';
        if ($request->hasFile('image_file')) {
            $imagePath = '/storage/' . $request->file('image_file')->store('menu-items', 'public');
        }

        $item = MenuItem::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
            'featured'    => $request->boolean('featured'),
            'is_archived' => $request->input('is_archived_flag', '0') === '1',
            'sort_order'  => MenuItem::where('category_id', $request->category_id)->max('sort_order') + 1,
        ]);

        $this->syncModifierGroups($item, $request->input('groups', []));
        $this->syncAddons($item, $request->input('addons', []));

        return back()->with('success', "Menu item \"{$item->name}\" created.");
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name'                                  => 'required|string|max:120',
            'category_id'                           => 'required|exists:categories,id',
            'price'                                 => 'required|numeric|min:0',
            'description'                           => 'nullable|string|max:400',
            'image_file'                            => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:4096',
            'featured'                              => 'nullable|boolean',
            'groups'                                => 'nullable|array',
            'groups.*.type'                         => 'required_with:groups|in:flavor,modifier,addon',
            'groups.*.name'                         => 'required_with:groups|string|max:80',
            'groups.*.required'                     => 'nullable|boolean',
            'groups.*.is_active'                    => 'nullable|boolean',
            'groups.*.options'                      => 'nullable|array',
            'groups.*.options.*.name'               => 'required_with:groups.*.options|string|max:80',
            'groups.*.options.*.price_type'         => 'required_with:groups.*.options|in:none,add,replace',
            'groups.*.options.*.price_adjustment'   => 'nullable|numeric|min:0',
            'groups.*.options.*.is_default'         => 'nullable|boolean',
            'groups.*.options.*.is_active'          => 'nullable|boolean',
        ]);

        // Handle image upload — use new file if provided, otherwise keep existing
        $imagePath = $menuItem->image;
        if ($request->hasFile('image_file')) {
            $imagePath = '/storage/' . $request->file('image_file')->store('menu-items', 'public');
        } elseif ($request->input('image_existing') !== null) {
            $imagePath = $request->input('image_existing') ?: $menuItem->image;
        }

        $menuItem->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
            'featured'    => $request->boolean('featured'),
            'is_archived' => $request->input('is_archived_flag', '0') === '1',
        ]);

        $this->syncModifierGroups($menuItem, $request->input('groups', []));
        $this->syncAddons($menuItem, $request->input('addons', []));

        return back()->with('success', "Menu item \"{$menuItem->name}\" updated.");
    }

    /**
     * Sync all modifier groups + options for a menu item in one pass.
     * Groups with an 'id' key are updated; without are created; any
     * existing groups whose IDs are missing are deleted.
     */
    private function syncModifierGroups(MenuItem $item, array $groups): void
    {
        $keptGroupIds = [];

        foreach ($groups as $idx => $groupData) {
            $groupPayload = [
                'menu_item_id' => $item->id,
                'type'         => $groupData['type'],
                'name'         => $groupData['name'],
                'required'     => !empty($groupData['required']),
                'is_active'    => isset($groupData['is_active']) ? (bool)$groupData['is_active'] : true,
                'sort_order'   => $idx,
            ];

            if (!empty($groupData['id'])) {
                $group = ModifierGroup::where('id', $groupData['id'])->where('menu_item_id', $item->id)->first();
                if ($group) $group->update($groupPayload);
            } else {
                $group = ModifierGroup::create($groupPayload);
            }

            if ($group) {
                $keptGroupIds[] = $group->id;
                $keptOptionIds  = [];
                $options        = $groupData['options'] ?? [];

                // Auto-prepend a "No X" default if none of the options is marked default
                $hasDefault = collect($options)->contains(fn($o) => !empty($o['is_default']));
                if (! $hasDefault) {
                    $defaultLabel = match($group->type) {
                        'flavor'   => 'No Flavor',
                        'modifier' => 'No ' . $group->name,
                        'addon'    => 'No Add-on',
                        default    => 'None',
                    };
                    array_unshift($options, [
                        'name'             => $defaultLabel,
                        'price_type'       => 'none',
                        'price_adjustment' => 0,
                        'is_default'       => true,
                        'is_active'        => true,
                    ]);
                }

                foreach ($options as $oIdx => $optData) {
                    $optPayload = [
                        'modifier_group_id' => $group->id,
                        'name'              => $optData['name'],
                        'price_type'        => $optData['price_type'] ?? 'none',
                        'price_adjustment'  => $optData['price_adjustment'] ?? 0,
                        'is_default'        => !empty($optData['is_default']),
                        'is_active'         => isset($optData['is_active']) ? (bool)$optData['is_active'] : true,
                        'sort_order'        => $oIdx,
                    ];

                    if (!empty($optData['id'])) {
                        $opt = ModifierOption::where('id', $optData['id'])->where('modifier_group_id', $group->id)->first();
                        if ($opt) $opt->update($optPayload);
                    } else {
                        $opt = ModifierOption::create($optPayload);
                    }

                    if ($opt ?? null) $keptOptionIds[] = $opt->id;
                }

                $group->options()->whereNotIn('id', $keptOptionIds)->delete();
            }
        }

        $item->modifierGroups()->whereNotIn('id', $keptGroupIds)->delete();
    }

    /**
     * Sync add-ons (stored as modifier_groups with type='addon').
     * Each addon is one group + one option (the price pairing).
     */
    private function syncAddons(MenuItem $item, array $addons): void
    {
        $keptIds = [];

        foreach ($addons as $i => $addonData) {
            $name      = $addonData['name']             ?? '';
            $desc      = $addonData['description']      ?? '';
            $pType     = $addonData['price_type']       ?? 'none';
            $pAdj      = $addonData['price_adjustment'] ?? 0;

            $groupPayload = [
                'menu_item_id' => $item->id,
                'type'         => 'addon',
                'name'         => $name,
                'required'     => false,
                'is_active'    => true,
                'sort_order'   => $i,
            ];

            if (!empty($addonData['id'])) {
                $group = ModifierGroup::where('id', $addonData['id'])
                                      ->where('menu_item_id', $item->id)
                                      ->first();
                if ($group) {
                    $group->update(array_merge($groupPayload, ['description' => $desc]));
                }
            } else {
                $group = ModifierGroup::create($groupPayload);
            }

            if ($group) {
                $keptIds[] = $group->id;

                // Each addon has exactly one option — the price entry
                $optPayload = [
                    'modifier_group_id' => $group->id,
                    'name'              => $name,
                    'price_type'        => $pType,
                    'price_adjustment'  => $pAdj,
                    'is_default'        => true,
                    'is_active'         => true,
                    'sort_order'        => 0,
                ];

                $existing = $group->options()->first();
                if ($existing) {
                    $existing->update($optPayload);
                } else {
                    ModifierOption::create($optPayload);
                }
            }
        }

        // Delete addons removed in the UI
        $item->modifierGroups()->where('type', 'addon')->whereNotIn('id', $keptIds)->delete();
    }

    // ── Standalone modifier group CRUD (for future API use) ──
    public function storeModifierGroup(Request $request, MenuItem $menuItem)
    {
        $request->validate(['type'=>'required|in:flavor,modifier','name'=>'required|string|max:80']);
        $group = $menuItem->modifierGroups()->create([
            'type'       => $request->type,
            'name'       => $request->name,
            'required'   => $request->boolean('required'),
            'is_active'  => true,
            'sort_order' => $menuItem->modifierGroups()->max('sort_order') + 1,
        ]);
        return response()->json($group->load('options'));
    }

    public function updateModifierGroup(Request $request, MenuItem $menuItem, ModifierGroup $group)
    {
        $request->validate(['name'=>'required|string|max:80']);
        $group->update($request->only('name','required','is_active'));
        return response()->json($group->load('options'));
    }

    public function deleteModifierGroup(Request $request, MenuItem $menuItem, ModifierGroup $group)
    {
        $group->delete();
        return response()->json(['success' => true]);
    }

    public function storeModifierOption(Request $request, ModifierGroup $group)
    {
        $request->validate(['name'=>'required|string|max:80','price_type'=>'required|in:none,add,replace']);
        $opt = $group->options()->create([
            'name'             => $request->name,
            'price_type'       => $request->price_type,
            'price_adjustment' => $request->input('price_adjustment', 0),
            'is_default'       => $request->boolean('is_default'),
            'is_active'        => true,
            'sort_order'       => $group->options()->max('sort_order') + 1,
        ]);
        return response()->json($opt);
    }

    public function updateModifierOption(Request $request, ModifierGroup $group, ModifierOption $option)
    {
        $request->validate(['name'=>'required|string|max:80','price_type'=>'required|in:none,add,replace']);
        $option->update($request->only('name','price_type','price_adjustment','is_default','is_active'));
        return response()->json($option);
    }

    public function deleteModifierOption(Request $request, ModifierGroup $group, ModifierOption $option)
    {
        $option->delete();
        return response()->json(['success' => true]);
    }

    public function archiveMenuItem(MenuItem $menuItem)
    {
        $menuItem->update(['is_archived' => ! $menuItem->is_archived]);
        $state = $menuItem->is_archived ? 'archived' : 'restored';
        return back()->with('success', "Menu item \"{$menuItem->name}\" {$state}.");
    }

    public function deleteMenuItem(MenuItem $menuItem)
    {
        $name = $menuItem->name;
        $menuItem->delete();
        return back()->with('success', "Menu item \"{$name}\" deleted.");
    }

    // ════════════════════════════════════════════════════════
    // ORDERS
    // ════════════════════════════════════════════════════════

    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['user', 'rider.user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        $statusCounts = [
            'pending'          => \App\Models\Order::where('status', 'pending')->count(),
            'preparing'        => \App\Models\Order::whereIn('status', ['accepted','preparing'])->count(),
            'out'              => \App\Models\Order::whereIn('status', ['rider_assigned','out_for_delivery'])->count(),
            'delivered'        => \App\Models\Order::where('status', 'delivered')->count(),
            'cancelled'        => \App\Models\Order::where('status', 'cancelled')->count(),
        ];

        $availableRiders = \App\Models\Rider::with('user')
            ->where('is_available', true)
            ->whereDoesntHave('orders', function ($q) {
                $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            })
            ->get();

        return view('admin.orders', compact('orders', 'statusCounts', 'availableRiders'));
    }

    // ── GET /admin/orders/poll — JSON snapshot for auto-refresh ──────────────
    public function ordersPoll(Request $request)
    {
        $query = \App\Models\Order::with(['user', 'rider.user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        $statusCounts = [
            'pending'      => \App\Models\Order::where('status', 'pending')->count(),
            'preparing'    => \App\Models\Order::whereIn('status', ['accepted','preparing'])->count(),
            'out'          => \App\Models\Order::whereIn('status', ['rider_assigned','out_for_delivery'])->count(),
            'delivered'    => \App\Models\Order::where('status', 'delivered')->count(),
            'cancelled'    => \App\Models\Order::where('status', 'cancelled')->count(),
            'today'        => \App\Models\Order::whereDate('created_at', today())->count(),
            'revenue_today'=> \App\Models\Order::where('status','delivered')->whereDate('delivered_at', today())->sum('total'),
        ];

        // Priority 1: free riders (available + not on active delivery)
        $availableRiders = \App\Models\Rider::with('user')
            ->where('is_available', true)
            ->whereDoesntHave('orders', function ($q) {
                $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            })
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'name' => $r->user->name, 'phone' => $r->phone, 'busy' => false]);

        // Priority 2: available riders who are busy (on delivery)
        if ($availableRiders->isEmpty()) {
            $availableRiders = \App\Models\Rider::with('user')
                ->where('is_available', true)
                ->withCount(['orders as active_orders' => function ($q) {
                    $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
                }])
                ->orderBy('active_orders')
                ->get()
                ->map(fn($r) => ['id' => $r->id, 'name' => $r->user->name, 'phone' => $r->phone, 'busy' => true]);
        }

        // Priority 3: ANY rider regardless of is_available flag
        if ($availableRiders->isEmpty()) {
            $availableRiders = \App\Models\Rider::with('user')
                ->withCount(['orders as active_orders' => function ($q) {
                    $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
                }])
                ->orderBy('active_orders')
                ->get()
                ->map(fn($r) => ['id' => $r->id, 'name' => $r->user->name, 'phone' => $r->phone, 'busy' => true]);
        }

        $ordersData = $orders->map(function ($o) {
            return [
                'id'               => $o->id,
                'order_number'     => $o->order_number,
                'status'           => $o->status,
                'order_type'       => $o->order_type,
                'order_type_label' => $o->order_type_label,
                'order_type_icon'  => $o->order_type_icon,
                'status_label'     => $o->status_label,
                'customer'         => $o->user?->name ?? 'Guest',
                'email'            => $o->user?->email ?? '',
                'address'          => $o->delivery_address,
                'delivery_lat'     => $o->delivery_lat,
                'delivery_lng'     => $o->delivery_lng,
                'table_number'     => $o->table_number,
                'payment'          => $o->payment_method,
                'subtotal'         => $o->subtotal,
                'delivery_fee'     => $o->delivery_fee,
                'total'            => $o->total,
                'notes'            => $o->notes,
                'date'             => $o->created_at->format('M d, Y g:i A'),
                'date_short'       => $o->created_at->format('M d g:i A'),
                'accepted_at'      => $o->accepted_at?->format('g:i A'),
                'prepared_at'      => $o->prepared_at ? true : false, // chef marked ready flag
                'picked_up_at'     => $o->picked_up_at?->format('g:i A'),
                'delivered_at'     => $o->delivered_at?->format('g:i A'),
                'rider'            => ($o->rider && $o->rider->user) ? $o->rider->user->name : null,
                'rider_lat'        => $o->rider?->current_lat,
                'rider_lng'        => $o->rider?->current_lng,
                'items'            => $o->items->map(fn($i) => [
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'price'     => $i->unit_price,
                    'subtotal'  => $i->subtotal,
                    'modifiers' => $i->modifiers ?? [],
                ])->toArray(),
            ];
        });

        return response()->json([
            'orders'        => $ordersData,
            'statusCounts'  => $statusCounts,
            'riders'        => $availableRiders,
        ]);
    }

    public function acceptOrder(\App\Models\Order $order)
    {
        if ($order->status !== 'pending') {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Order cannot be accepted.'], 422)
                : back()->with('error', 'Order cannot be accepted.');
        }

        // Accept the order — chef will start cooking manually
        $order->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        broadcast(new OrderStatusUpdated($order));

        if (request()->expectsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => "Order #{$order->order_number} accepted & sent to kitchen.",
                'receipt_url' => route('chef.orders.receipt', $order->id),
            ]);
        }

        return back()->with('success', "Order #{$order->order_number} accepted & sent to kitchen.");
    }

    public function assignRider(Request $request, \App\Models\Order $order)
    {
        $request->validate(['rider_id' => 'required|exists:riders,id']);

        if (!$order->isAssignable()) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Order cannot be assigned at this stage.'], 422)
                : back()->with('error', 'Order cannot be assigned at this stage.');
        }

        $order->update([
            'rider_id'    => $request->rider_id,
            'status'      => 'rider_assigned',
            'assigned_at' => now(),
            'prepared_at' => $order->prepared_at ?? now(),
        ]);

        broadcast(new OrderStatusUpdated($order));

        return request()->expectsJson()
            ? response()->json(['success' => true, 'message' => "Rider assigned to order #{$order->order_number}."])
            : back()->with('success', "Rider assigned to order #{$order->order_number}.");
    }

    public function updateOrderStatus(Request $request, \App\Models\Order $order)
    {
        // Admin transitions: cancel anytime, or mark delivered for pickup/dine-in
        // preparing→preparing (chef marks ready via prepared_at, not status change)
        // out_for_delivery is rider-owned
        $allowed = ['cancelled'];
        if ($order->order_type !== 'delivery') {
            $allowed[] = 'delivered';
        }

        $request->validate(['status' => ['required', Rule::in($allowed)]]);

        if ($order->status === 'delivered') {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Delivered orders cannot be changed.'], 422)
                : back()->with('error', 'Delivered orders cannot be changed.');
        }

        if ($request->status === 'out_for_delivery') {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Only the assigned rider can mark an order as out for delivery.'], 403)
                : back()->with('error', 'Only the assigned rider can mark this order as out for delivery.');
        }

        $data = ['status' => $request->status];

        // Stamp the appropriate timestamp
        match($request->status) {
            'accepted'         => $data['accepted_at']  = now(),
            'out_for_delivery' => $data['picked_up_at'] = now(),
            'delivered'        => $data['delivered_at'] = now(),
            'cancelled'        => $data['cancelled_at'] = now(),
            default            => null,
        };

        $order->update($data);

        broadcast(new OrderStatusUpdated($order));

        if (request()->expectsJson()) {
            $response = [
                'success' => true,
                'message' => "Order #{$order->order_number} updated to \"{$request->status}\".",
            ];
            // Auto-print receipt when admin marks as complete (delivered)
            if ($request->status === 'delivered') {
                $response['receipt_url'] = route('chef.orders.receipt', $order->id);
            }
            return response()->json($response);
        }

        return back()->with('success', "Order #{$order->order_number} updated to \"{$request->status}\".");
    }

    public function riderLocations()
    {
        // Include ALL riders who have a GPS position — both available AND actively delivering
        $riders = \App\Models\Rider::with(['user', 'orders' => function($q) {
                $q->with('user')->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            }])
            ->where(function ($q) {
                $q->where('is_available', true)
                  ->orWhereHas('orders', fn($oq) => $oq->whereIn('status', ['rider_assigned', 'out_for_delivery']));
            })
            ->whereNotNull('current_lat')
            ->get()
            ->map(function ($r) {
                $activeOrders = $r->orders; // already eager-loaded with filter above
                $isOnDelivery = $activeOrders->isNotEmpty();

                // Build one destination entry per active order
                $destinations = $activeOrders->map(fn($o) => [
                    'order_number' => $o->order_number,
                    'customer'     => $o->user?->name,
                    'address'      => $o->delivery_address,
                    'dest_lat'     => $o->delivery_lat,
                    'dest_lng'     => $o->delivery_lng,
                    'status'       => $o->status,
                ])->values()->all();

                return [
                    'id'           => $r->id,
                    'name'         => $r->user->name,
                    'lat'          => $r->current_lat,
                    'lng'          => $r->current_lng,
                    'status'       => $isOnDelivery ? 'On Delivery' : 'Online',
                    'order_count'  => $activeOrders->count(),
                    'orders'       => $destinations,
                    // Legacy single-order fields (kept for backwards compat)
                    'order'        => $activeOrders->first()?->order_number,
                    'dest_lat'     => $activeOrders->first()?->delivery_lat,
                    'dest_lng'     => $activeOrders->first()?->delivery_lng,
                    'customer'     => $activeOrders->first()?->user?->name,
                    'address'      => $activeOrders->first()?->delivery_address,
                ];
            });

        return response()->json($riders);
    }

    // ════════════════════════════════════════════════════════
    // RIDERS
    // ════════════════════════════════════════════════════════

    public function riders(Request $request)
    {
        $riders = \App\Models\Rider::with(['user', 'orders' => function($q) {
                $q->with('user')->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            }])
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'online') {
                    $q->where('is_available', true);
                } elseif ($request->status === 'offline') {
                    $q->where('is_available', false);
                }
            })
            ->get();

        return view('admin.riders', compact('riders'));
    }

    public function storeRider(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|string|max:20',
            'vehicle_type' => 'required|in:motorcycle,bicycle',
            'plate_number' => 'nullable|string|max:20',
            'password'     => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'rider',
        ]);

        \App\Models\Rider::create([
            'user_id'      => $user->id,
            'phone'        => $request->phone,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
            'is_available' => false,
        ]);

        return back()->with('success', "Rider {$user->name} created successfully.");
    }

    public function updateRider(Request $request, \App\Models\Rider $rider)
    {
        $request->validate([
            'phone'        => 'nullable|string|max:20',
            'vehicle_type' => 'required|in:motorcycle,bicycle',
            'plate_number' => 'nullable|string|max:20',
        ]);

        $rider->update($request->only('phone', 'vehicle_type', 'plate_number'));
        return back()->with('success', 'Rider updated.');
    }

    public function removeRider(\App\Models\Rider $rider)
    {
        $rider->user->update(['role' => 'user']);
        $rider->delete();
        return back()->with('success', 'Rider removed.');
    }

    // ════════════════════════════════════════════════════════
    // SETTINGS
    // ════════════════════════════════════════════════════════

    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:100',
            'contact_email'   => 'required|email|max:150',
            'contact_phone'   => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:255',
            'delivery_fee'    => 'nullable|numeric|min:0',
            'min_order'       => 'nullable|numeric|min:0',
        ]);

        return back()->with('success', 'Settings saved successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated successfully.');
    }
}
