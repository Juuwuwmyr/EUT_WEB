<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Http\Middleware\RequireAdminVerification;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ════════════════════════════════════════════════════════
    // CREDENTIAL RE-VERIFICATION (Menu & Categories gate)
    // ════════════════════════════════════════════════════════

    public function showVerify()
    {
        $scope = session('admin_verify_scope', 'menu');

        if (! in_array($scope, RequireAdminVerification::SCOPES, true)) {
            $scope = 'menu';
        }

        return view('admin.verify', compact('scope'));
    }

    public function submitVerify(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'scope'    => ['nullable', Rule::in(RequireAdminVerification::SCOPES)],
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }

        $scope = $request->input('scope')
            ?? session('admin_verify_scope')
            ?? 'dashboard';

        if (! in_array($scope, RequireAdminVerification::SCOPES, true)) {
            $scope = 'dashboard';
        }

        RequireAdminVerification::markVerified($scope);
        session()->forget('admin_verify_scope');

        $defaultIntended = match ($scope) {
            'dashboard'  => route('admin.dashboard'),
            'categories' => route('admin.categories'),
            'menu'       => route('admin.menu-items'),
            default      => route('admin.dashboard'),
        };

        $intended = session()->pull('admin_verify_intended', $defaultIntended);

        return redirect($intended);
    }

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
            'waiter_users'     => User::where('role', 'waiter')->count(),
            'active_riders'    => \App\Models\Rider::where('is_available', true)->count(),
            'total_items'      => MenuItem::active()->count(),
            'total_categories' => Category::active()->count(),
            'featured_items'   => MenuItem::featured()->count(),
            'total_orders'     => \App\Models\Order::count(),
            'today_orders'     => \App\Models\Order::whereDate('created_at', today())->count(),
            'items_sold_today' => \App\Models\OrderItem::whereHas('order', fn($q) => $q->whereDate('created_at', today()))->sum('quantity'),
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

        // Top-selling menu items today (by quantity sold from today's orders, excluding cancelled/archived)
        $topItems = \App\Models\OrderItem::select(
                'menu_item_id',
                \Illuminate\Support\Facades\DB::raw('MAX(item_name) as item_name'),
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'),
                \Illuminate\Support\Facades\DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', fn($q) => $q
                ->whereDate('created_at', today())
                ->whereNotIn('status', ['cancelled'])
            )
            ->whereNotNull('menu_item_id')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_sold')
            ->get()
            ->map(function($i) {
                $menuItem = \App\Models\MenuItem::with('category')->find($i->menu_item_id);
                return [
                    'name'           => $menuItem?->name ?? $i->item_name,
                    'total_sold'     => (int) $i->total_sold,
                    'total_revenue'  => (float) $i->total_revenue,
                    'image'          => $menuItem?->image ?? '/images/hero-burger.webp',
                    'category'       => $menuItem?->category?->name ?? '—',
                    'category_color' => $menuItem?->category?->color ?? '#6b7280',
                ];
            })->toArray();

        $dashboardVerified = RequireAdminVerification::isVerified('dashboard');

        if ($dashboardVerified) {
            RequireAdminVerification::clearVerified('dashboard');
        } else {
            session([
                'admin_verify_intended' => route('admin.dashboard'),
                'admin_verify_scope'    => 'dashboard',
            ]);
        }

        return view('admin.dashboard', compact('stats', 'recent_users', 'categories', 'topItems', 'dashboardVerified'));
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
            'role'         => ['required', Rule::in(['admin', 'user', 'chef', 'rider', 'waiter'])],
            'phone'        => 'required_if:role,rider|nullable|string|max:20',
            'plate_number' => 'nullable|string|max:30',
        ]);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => $request->password, // model cast handles hashing
            'role'             => $request->role,
            'provider'         => 'email',
            'email_verified_at'=> now(),              // staff accounts don't need email verification
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
            'role'  => ['required', Rule::in(['admin', 'user', 'chef', 'rider', 'waiter'])],
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $data = ['name' => $request->name, 'email' => $request->email, 'role' => $request->role];
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = $request->password; // model cast handles hashing
        }

        $user->update($data);
        return back()->with('success', "User \"{$user->name}\" updated successfully.");
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => ['required', Rule::in(['admin', 'user', 'chef', 'rider', 'waiter'])]]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }
        $user->updateQuietly(['role' => $request->role]);

        AuditLog::record(
            action:      'role_changed',
            description: "Admin changed \"{$user->name}\" role to {$request->role}.",
            model:       $user,
            newValues:   ['role' => $request->role],
        );

        return back()->with('success', "Role updated to {$request->role}.");
    }

    public function archiveUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot archive your own account.');
        }
        // Uses soft "role" disable — sets role to 'archived' to block login
        $user->updateQuietly(['role' => 'archived']);

        AuditLog::record(
            action:      'user_archived',
            description: "Admin archived user \"{$user->name}\".",
            model:       $user,
            newValues:   ['role' => 'archived'],
        );

        return back()->with('success', "User \"{$user->name}\" archived.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->deleteQuietly();

        AuditLog::record(
            action:      'user_deleted',
            description: "Admin deleted user \"{$name}\".",
        );

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
        $category->updateQuietly(['is_archived' => ! $category->is_archived]);
        $category->refresh();
        $state = $category->is_archived ? 'archived' : 'restored';

        AuditLog::record(
            action:      $state,
            description: "Admin {$state} category \"{$category->name}\".",
            model:       $category,
        );

        return back()->with('success', "Category \"{$category->name}\" {$state}.");
    }

    public function deleteCategory(Category $category)
    {
        if ($category->menuItems()->exists()) {
            return back()->with('error', "Cannot delete \"{$category->name}\" — it still has menu items. Archive it or move items first.");
        }
        $name = $category->name;
        $category->deleteQuietly();

        AuditLog::record(
            action:      'category_deleted',
            description: "Admin deleted category \"{$name}\".",
        );

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
        } elseif ($request->boolean('all')) {
            // Show all items — both active and archived
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
        \Log::info('storeMenuItem payload', [
            'groups' => $request->input('groups'),
            'addons' => $request->input('addons'),
            'all'    => $request->except(['_token', 'image_file']),
        ]);

        try {
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

        // Handle image upload — always converted to WebP for a lighter, consistent shop
        $imagePath = '/images/hero-burger.webp';
        if ($request->hasFile('image_file')) {
            $imagePath = '/storage/' . \App\Services\ImageUploadService::storeAsWebp($request->file('image_file'), 'menu-items');
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

        return redirect()->route('admin.menu-items')->with('success', "Menu item \"{$item->name}\" created.");
        } catch (\Exception $e) {
            \Log::error('storeMenuItem error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.menu-items')->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        \Log::info('updateMenuItem payload', [
            'id'     => $menuItem->id,
            'groups' => $request->input('groups'),
            'addons' => $request->input('addons'),
        ]);

        try {
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
        // (always converted to WebP for a lighter, consistent shop)
        $imagePath = $menuItem->image;
        if ($request->hasFile('image_file')) {
            $imagePath = '/storage/' . \App\Services\ImageUploadService::storeAsWebp($request->file('image_file'), 'menu-items');
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

        return redirect()->route('admin.menu-items')->with('success', "Menu item \"{$menuItem->name}\" updated.");
        } catch (\Exception $e) {
            \Log::error('updateMenuItem error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.menu-items')->with('error', 'Failed to save: ' . $e->getMessage());
        }
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
            $group = null; // reset each iteration to avoid stale references

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
                if ($group) {
                    $group->update($groupPayload);
                } else {
                    // ID not found — treat as new
                    $group = ModifierGroup::create($groupPayload);
                }
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
                    $opt = null; // reset each iteration

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
                        if ($opt) {
                            $opt->update($optPayload);
                        } else {
                            // ID not found — treat as new
                            $opt = ModifierOption::create($optPayload);
                        }
                    } else {
                        $opt = ModifierOption::create($optPayload);
                    }

                    if ($opt) $keptOptionIds[] = $opt->id;
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
            $group = null; // reset each iteration to avoid stale references

            $name      = $addonData['name']             ?? '';
            $desc      = $addonData['description']      ?? '';
            $pType     = $addonData['price_type']       ?? 'none';
            $pAdj      = $addonData['price_adjustment'] ?? 0;

            $groupPayload = [
                'menu_item_id'   => $item->id,
                'type'           => 'addon',
                'name'           => $name,
                'required'       => false,
                'is_active'      => true,
                'sort_order'     => $i,
                'max_selections' => isset($addonData['max_selections']) && $addonData['max_selections'] !== ''
                                        ? (int) $addonData['max_selections']
                                        : null,
            ];

            if (!empty($addonData['id'])) {
                $group = ModifierGroup::where('id', $addonData['id'])
                                      ->where('menu_item_id', $item->id)
                                      ->first();
                if ($group) {
                    $group->update(array_merge($groupPayload, ['description' => $desc]));
                } else {
                    // ID not found — treat as new
                    $group = ModifierGroup::create($groupPayload);
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

        // Delete addons removed in the UI.
        // IMPORTANT: Only delete simple addon groups (≤1 option) that were managed
        // through the admin UI. Multi-option addon groups (e.g. Drink with 7 options)
        // inserted via SQL are NOT rendered in the admin UI, so never delete them here.
        $multiOptionIds = $item->modifierGroups()
            ->where('type', 'addon')
            ->whereNotIn('id', $keptIds)
            ->withCount('options')
            ->get()
            ->filter(fn($g) => $g->options_count > 1)
            ->pluck('id');

        $item->modifierGroups()
             ->where('type', 'addon')
             ->whereNotIn('id', $keptIds)
             ->whereNotIn('id', $multiOptionIds)
             ->delete();
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
        $menuItem->updateQuietly(['is_archived' => ! $menuItem->is_archived]);
        $menuItem->refresh();
        $state = $menuItem->is_archived ? 'archived' : 'restored';

        AuditLog::record(
            action:      $state,
            description: "Admin {$state} menu item \"{$menuItem->name}\".",
            model:       $menuItem,
        );

        if (request()->expectsJson()) {
            return response()->json([
                'success'     => true,
                'is_archived' => $menuItem->is_archived,
                'state'       => $state,
            ]);
        }

        return back()->with('success', "Menu item \"{$menuItem->name}\" {$state}.");
    }

    public function deleteMenuItem(MenuItem $menuItem)
    {
        $name = $menuItem->name;
        $menuItem->deleteQuietly();

        AuditLog::record(
            action:      'deleted',
            description: "Admin deleted menu item \"{$name}\".",
        );

        return back()->with('success', "Menu item \"{$name}\" deleted.");
    }

    // ════════════════════════════════════════════════════════
    // ORDERS
    // ════════════════════════════════════════════════════════

    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['user', 'rider.user', 'items']);

        // Default: today's non-archived orders. Toggle with ?all=1 or ?archived=1
        if ($request->boolean('archived')) {
            $query->where('is_archived', true)->orderByDesc('created_at');
        } else if ($request->boolean('all')) {
            $query->where('is_archived', false);
        } else {
            // Default view: today's orders only (non-archived)
            $query->whereDate('created_at', today())->where('is_archived', false);
        }

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
            ->withCount(['orders as active_orders' => function ($q) {
                $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            }])
            ->orderBy('active_orders') // free riders first
            ->get();

        return view('admin.orders', compact('orders', 'statusCounts', 'availableRiders'));
    }

    // ── GET /admin/orders/poll — JSON snapshot for auto-refresh ──────────────
    public function ordersPoll(Request $request)
    {
        $query = \App\Models\Order::with(['user', 'rider.user', 'items']);

        // Default: today's non-archived orders. Toggle with ?all=1 or ?archived=1
        if ($request->boolean('archived')) {
            $query->where('is_archived', true)->orderByDesc('created_at');
        } else if ($request->boolean('all')) {
            $query->where('is_archived', false);
        } else {
            $query->whereDate('created_at', today())->where('is_archived', false);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination for archived view only
        $isArchived = $request->boolean('archived');
        $perPage    = 20;
        $page       = max(1, (int) $request->input('page', 1));
        $totalCount = null;
        $totalPages = null;

        if ($isArchived) {
            $totalCount = $query->count();
            $totalPages = (int) ceil($totalCount / $perPage);
            $orders = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $orders = $query->latest()->get();
        }

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
            ->withCount(['orders as active_orders' => function ($q) {
                $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            }])
            ->orderBy('active_orders')
            ->get()
            ->map(fn($r) => [
                'id'    => $r->id,
                'name'  => $r->user->name . ($r->active_orders > 0 ? ' 🏍️ (' . $r->active_orders . ' order' . ($r->active_orders > 1 ? 's' : '') . ')' : ''),
                'phone' => $r->phone,
                'busy'  => $r->active_orders > 0,
            ]);

        // Priority 2: ANY rider regardless of is_available flag (fallback)
        if ($availableRiders->isEmpty()) {
            $availableRiders = \App\Models\Rider::with('user')
                ->withCount(['orders as active_orders' => function ($q) {
                    $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
                }])
                ->orderBy('active_orders')
                ->get()
                ->map(fn($r) => [
                    'id'    => $r->id,
                    'name'  => $r->user->name . ($r->active_orders > 0 ? ' 🏍️ (' . $r->active_orders . ' order' . ($r->active_orders > 1 ? 's' : '') . ')' : ''),
                    'phone' => $r->phone,
                    'busy'  => true,
                ]);
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
                'phone'            => $o->user?->phone ?? '',
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
                'is_archived'      => (bool) $o->is_archived,
                'accepted_at'      => $o->accepted_at?->format('g:i A'),
                'prepared_at'      => $o->prepared_at ? true : false, // chef marked ready flag
                'picked_up_at'     => $o->picked_up_at?->format('g:i A'),
                'delivered_at'     => $o->delivered_at?->format('g:i A'),
                'rider'            => ($o->rider && $o->rider->user) ? $o->rider->user->name : null,
                'rider_lat'        => $o->rider?->current_lat,
                'rider_lng'        => $o->rider?->current_lng,
                'items'            => $o->items->map(fn($i) => [
                    'id'        => $i->id,
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'price'     => $i->unit_price,
                    'subtotal'  => $i->subtotal,
                    'modifiers' => $i->modifiers ?? [],
                ])->toArray(),
                'ordering_locked'  => (bool) $o->ordering_locked,
                'table_session_id' => $o->table_session_id,
            ];
        });

        return response()->json([
            'orders'        => $ordersData,
            'statusCounts'  => $statusCounts,
            'riders'        => $availableRiders,
            'pagination'    => $isArchived ? [
                'page'       => $page,
                'perPage'    => $perPage,
                'total'      => $totalCount,
                'totalPages' => $totalPages,
            ] : null,
        ]);
    }

    public function acceptOrder(\App\Models\Order $order)
    {
        // Already accepted — treat as success (idempotent)
        if ($order->status === 'accepted') {
            return request()->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Order already accepted.'])
                : back()->with('success', 'Order already accepted.');
        }

        if ($order->status !== 'pending') {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Order cannot be accepted (status: ' . $order->status . ').'], 422)
                : back()->with('error', 'Order cannot be accepted.');
        }

        // Accept the order — chef will start cooking manually
        $order->updateQuietly([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);
        $order->refresh();

        // Queue kitchen ticket for auto-print agent.
        // updateOrInsert prevents duplicate rows if the admin somehow
        // accepts the same order twice (double-click / network retry).
        \Illuminate\Support\Facades\DB::table('kitchen_print_jobs')->updateOrInsert(
            ['order_id' => $order->id, 'type' => 'ticket'],
            ['printed' => false, 'printed_at' => null, 'created_at' => now(), 'updated_at' => now()]
        );

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'order_accepted',
            description: "Admin accepted {$order->order_number}.",
            model:       $order,
        );

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Order #{$order->order_number} accepted & sent to kitchen."]);
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

        $order->updateQuietly([
            'rider_id'    => $request->rider_id,
            // Keep out_for_delivery status if already on the way, otherwise set rider_assigned
            'status'      => $order->status === 'out_for_delivery' ? 'out_for_delivery' : 'rider_assigned',
            'assigned_at' => now(),
            'prepared_at' => $order->prepared_at ?? now(),
        ]);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'rider_assigned',
            description: "Admin assigned rider to {$order->order_number}.",
            model:       $order,
            newValues:   ['rider_id' => $request->rider_id],
        );

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

        $order->updateQuietly($data);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'order_status_updated',
            description: "Admin updated {$order->order_number} to \"{$request->status}\".",
            model:       $order,
            newValues:   ['status' => $request->status],
        );

        if (request()->expectsJson()) {
            $response = [
                'success' => true,
                'message' => "Order #{$order->order_number} updated to \"{$request->status}\".",
            ];
            // Auto-print receipt when admin marks as complete (delivered)
            if ($request->status === 'delivered') {
                // Save cash received and change for dine-in orders
                if ($order->order_type === 'dine_in') {
                    $cashReceived = (float) $request->input('cash_received', 0);
                    $changeDue    = $cashReceived > 0 ? round($cashReceived - $order->total, 2) : null;
                    $paymentUpdate = [
                        'payment_status' => 'paid',
                        'payment_method' => 'cash',
                    ];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'cash_received')) {
                        $paymentUpdate['cash_received'] = $cashReceived > 0 ? $cashReceived : null;
                        $paymentUpdate['change_due']    = $changeDue;
                    }
                    $order->updateQuietly($paymentUpdate);
                    $order->refresh();
                    $this->lockTableSessionIfClosed($order);
                }
                // Generate receipt
                if ($order->order_type === 'dine_in' && $order->table_number) {
                    $response['receipt_url'] = route('chef.orders.table-receipt', [
                        'order' => $order->id,
                        'ids'   => (string) $order->id,
                    ]);
                } else {
                    $response['receipt_url'] = route('chef.orders.receipt', $order->id);
                }
            }
            return response()->json($response);
        }

        return back()->with('success', "Order #{$order->order_number} updated to \"{$request->status}\".");
    }

    /**
     * Complete ALL active orders for a table session at once.
     * Marks every preparing/accepted order at the same table as delivered
     * and returns a single combined receipt URL.
     */
    public function completeTable(\App\Models\Order $order)
    {
        if (!$order->table_number || $order->order_type !== 'dine_in') {
            return response()->json(['success' => false, 'message' => 'Not a dine-in table order.'], 422);
        }

        // Find all active orders for this table session
        $tableOrders = \App\Models\Order::where('table_number', $order->table_number)
            ->where('order_type', 'dine_in')
            ->whereIn('status', ['preparing', 'accepted', 'pending'])
            ->whereDate('created_at', today())
            ->when($order->table_session_id, fn($q) => $q->where('table_session_id', $order->table_session_id))
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active orders found for this table.'], 422);
        }

        $receiptOrderIds = $tableOrders->pluck('id')->implode(',');

        $cashReceived = (float) request()->input('cash_received', 0);
        $grandTotal   = $tableOrders->sum('total');
        $changeDue    = $cashReceived > 0 ? round($cashReceived - $grandTotal, 2) : null;

        $now = now();
        $hasOrderingLocked = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'ordering_locked');

        foreach ($tableOrders as $o) {
            $updateData = [
                'status'         => 'delivered',
                'delivered_at'   => $now,
                'prepared_at'    => $o->prepared_at ?? $now,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
            ];

            // Lock session so the next customer at this table gets a fresh group.
            if ($hasOrderingLocked) {
                $updateData['ordering_locked'] = true;
            }

            // Only save payment fields if the columns exist
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'cash_received')) {
                $updateData['cash_received'] = $cashReceived > 0 ? $cashReceived : null;
                $updateData['change_due']    = $changeDue;
            }

            $o->updateQuietly($updateData);
            broadcast(new \App\Events\OrderStatusUpdated($o))->toOthers();
        }

        // Also lock any already-delivered orders in the same session (e.g. prior pahabol).
        if ($hasOrderingLocked && $order->table_session_id) {
            \App\Models\Order::where('order_type', 'dine_in')
                ->where('table_number', $order->table_number)
                ->where('table_session_id', $order->table_session_id)
                ->whereDate('created_at', today())
                ->where('ordering_locked', false)
                ->update(['ordering_locked' => true]);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Table ' . $order->table_number . ' — all orders completed.',
            'receipt_url' => route('chef.orders.table-receipt', [
                'order' => $order->id,
                'ids'   => $receiptOrderIds,
            ]),
        ]);
    }

    // completeTable audit
    // (recorded inline above via the Order model's Auditable trait per-row)

    /**
     * Lock a dine-in table session once every order in it is delivered or cancelled.
     * Ensures the next customer at the same physical table gets a fresh session.
     */
    private function lockTableSessionIfClosed(\App\Models\Order $order): void
    {
        if ($order->order_type !== 'dine_in' || ! $order->table_number || ! $order->table_session_id) {
            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('orders', 'ordering_locked')) {
            return;
        }

        $sessionOrders = \App\Models\Order::where('order_type', 'dine_in')
            ->where('table_number', $order->table_number)
            ->where('table_session_id', $order->table_session_id)
            ->whereDate('created_at', today())
            ->get();

        if ($sessionOrders->isEmpty()) {
            return;
        }

        $allClosed = $sessionOrders->every(
            fn (\App\Models\Order $o) => in_array($o->status, ['delivered', 'cancelled'], true)
        );

        if (! $allClosed) {
            return;
        }

        foreach ($sessionOrders as $o) {
            if (! $o->ordering_locked) {
                $o->updateQuietly(['ordering_locked' => true]);
                broadcast(new OrderStatusUpdated($o))->toOthers();
            }
        }
    }

    /**
     * Lock ordering for a dine-in table session.
     *
     * Sets ordering_locked = true on every active (non-delivered/non-cancelled)
     * order in the same table session.  After this:
     *   - No subsequent pahabol order from the customer will be merged in.
     *   - A new customer ordering at the same table always gets a fresh session.
     */
    public function lockTable(\App\Models\Order $order)
    {
        if (!$order->table_number || $order->order_type !== 'dine_in') {
            return response()->json(['success' => false, 'message' => 'Not a dine-in table order.'], 422);
        }

        // Find all non-closed orders for this table session
        $tableOrders = \App\Models\Order::where('order_type', 'dine_in')
            ->where('table_number', $order->table_number)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->whereDate('created_at', today())
            ->when(
                $order->table_session_id,
                fn($q) => $q->where('table_session_id', $order->table_session_id)
            )
            ->get();

        if ($tableOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No active orders found for this table.'], 422);
        }

        foreach ($tableOrders as $o) {
            $o->updateQuietly(['ordering_locked' => true]);
            broadcast(new \App\Events\OrderStatusUpdated($o))->toOthers();
        }

        return response()->json([
            'success' => true,
            'message' => 'Table ' . $order->table_number . ' — ordering locked. No more pahabol orders will be merged.',
            'locked_count' => $tableOrders->count(),
        ]);
    }

    public function archiveOrder(\App\Models\Order $order)
    {
        // Only allow archiving completed/cancelled orders
        if (!in_array($order->status, ['delivered', 'cancelled'])) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Only completed or cancelled orders can be archived.'], 422)
                : back()->with('error', 'Only completed or cancelled orders can be archived.');
        }

        $archiving = ! $order->is_archived;

        $order->updateQuietly([
            'is_archived' => $archiving,
            'archived_at' => $archiving ? now() : null,
        ]);
        $order->refresh();

        $label = $archiving ? 'archived' : 'restored';

        AuditLog::record(
            action:      $label,
            description: "Admin {$label} order {$order->order_number}.",
            model:       $order,
        );

        return request()->expectsJson()
            ? response()->json(['success' => true, 'message' => "Order #{$order->order_number} {$label}.", 'is_archived' => $order->is_archived])
            : back()->with('success', "Order #{$order->order_number} {$label}.");
    }

    /**
     * Bulk archive/restore a set of orders (e.g. all orders in a dine-in table session).
     *
     * Route: PATCH /admin/orders/bulk-archive
     * Body:  { "ids": [1, 2, 3], "archive": true }
     */
    public function bulkArchiveOrders(Request $request)
    {
        $request->validate([
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'integer|exists:orders,id',
            'archive' => 'boolean',
        ]);

        $archiving = $request->boolean('archive', true);

        $orders = \App\Models\Order::whereIn('id', $request->ids)
            ->whereIn('status', ['delivered', 'cancelled'])
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No eligible orders found to archive.'], 422);
        }

        foreach ($orders as $order) {
            $order->updateQuietly([
                'is_archived' => $archiving,
                'archived_at' => $archiving ? now() : null,
            ]);
        }

        $label = $archiving ? 'archived' : 'restored';
        $count = $orders->count();

        AuditLog::record(
            action:      $label,
            description: "Admin bulk {$label} {$count} order(s): " . $orders->pluck('order_number')->join(', ') . '.',
        );

        return response()->json([
            'success' => true,
            'message' => "{$count} order(s) {$label} successfully.",
            'count'   => $count,
        ]);
    }

    public function deleteOrder(\App\Models\Order $order)
    {
        // Only allow deleting archived orders (safety check)
        if (!$order->is_archived) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Archive the order first before deleting.'], 422)
                : back()->with('error', 'Archive the order first before deleting.');
        }

        $orderNum = $order->order_number;
        $order->items()->delete();
        $order->deleteQuietly();

        AuditLog::record(
            action:      'order_deleted',
            description: "Admin permanently deleted order {$orderNum}.",
        );

        return request()->expectsJson()
            ? response()->json(['success' => true, 'message' => "Order {$orderNum} permanently deleted."])
            : back()->with('success', "Order {$orderNum} permanently deleted.");
    }

    // ════════════════════════════════════════════════════════
    // MONTHLY RESET — archive orders to JSON, then hard-delete
    // ════════════════════════════════════════════════════════

    /**
     * POST /admin/orders/reset-month
     *
     * 1. Collects all delivered / cancelled orders (with their items) for the
     *    chosen month-year (defaults to the previous calendar month).
     * 2. Serialises the full dataset to
     *    storage/app/archives/orders-YYYY-MM.json  (appends if file exists).
     * 3. Hard-deletes those orders (and their order_items) from the database.
     * 4. Writes one AuditLog entry.
     *
     * Body params (optional):
     *   month  — 1–12 (default: previous month)
     *   year   — 4-digit year (default: year of previous month)
     */
    public function resetOrders(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year'  => 'nullable|integer|min:2020|max:2099',
        ]);

        // Default: previous calendar month
        $refDate = now()->subMonthNoOverflow()->startOfMonth();
        $month   = (int) $request->input('month', $refDate->month);
        $year    = (int) $request->input('year',  $refDate->year);

        $periodStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd   = $periodStart->copy()->endOfMonth();

        // ── 1. Fetch eligible orders ─────────────────────────────────────
        $orders = \App\Models\Order::with(['items', 'user'])
            ->whereIn('status', ['delivered', 'cancelled'])
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No completed or cancelled orders found for {$periodStart->format('F Y')}.",
            ], 422);
        }

        // ── 2. Build archive payload ─────────────────────────────────────
        $archiveLabel = $periodStart->format('Y-m');
        $archivePath  = "archives/orders-{$archiveLabel}.json";

        $existingData = [];
        if (Storage::disk('local')->exists($archivePath)) {
            $raw          = Storage::disk('local')->get($archivePath);
            $existingData = json_decode($raw, true) ?? [];
        }

        $newEntries = $orders->map(function (\App\Models\Order $o) {
            return [
                'id'               => $o->id,
                'order_number'     => $o->order_number,
                'user_id'          => $o->user_id,
                'customer_name'    => $o->user?->name ?? 'Guest',
                'customer_email'   => $o->user?->email,
                'order_type'       => $o->order_type,
                'status'           => $o->status,
                'subtotal'         => $o->subtotal,
                'delivery_fee'     => $o->delivery_fee,
                'total'            => $o->total,
                'payment_method'   => $o->payment_method,
                'payment_status'   => $o->payment_status,
                'cash_received'    => $o->cash_received,
                'change_due'       => $o->change_due,
                'delivery_address' => $o->delivery_address,
                'table_number'     => $o->table_number,
                'notes'            => $o->notes,
                'cancel_reason'    => $o->cancel_reason,
                'created_at'       => optional($o->created_at)->toIso8601String(),
                'delivered_at'     => optional($o->delivered_at)->toIso8601String(),
                'cancelled_at'     => optional($o->cancelled_at)->toIso8601String(),
                'items'            => $o->items->map(fn($i) => [
                    'id'         => $i->id,
                    'name'       => $i->item_name,
                    'quantity'   => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'subtotal'   => $i->subtotal,
                    'modifiers'  => $i->modifiers,
                ])->toArray(),
                'archived_at' => now()->toIso8601String(),
            ];
        })->toArray();

        $mergedData = array_merge($existingData, $newEntries);

        Storage::disk('local')->put(
            $archivePath,
            json_encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // ── 3. Hard-delete from database ────────────────────────────────
        $orderIds = $orders->pluck('id')->toArray();

        DB::transaction(function () use ($orderIds) {
            \App\Models\OrderItem::whereIn('order_id', $orderIds)->delete();
            \App\Models\Order::whereIn('id', $orderIds)->delete();
        });

        // ── 4. Audit log ────────────────────────────────────────────────
        $count = count($orderIds);

        AuditLog::record(
            action:      'monthly_reset',
            description: "Admin reset {$count} order(s) for {$periodStart->format('F Y')}. "
                       . "Archived to {$archivePath}.",
        );

        return response()->json([
            'success'      => true,
            'message'      => "{$count} order(s) for {$periodStart->format('F Y')} have been archived and removed from the database.",
            'archive_file' => $archivePath,
            'count'        => $count,
            'period'       => $periodStart->format('F Y'),
        ]);
    }

    public function riderLocations()    {
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
            ->whereHas('user') // exclude orphaned riders with no user account
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

        $user = \App\Models\User::withoutEvents(function () use ($request) {
            return \App\Models\User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'role'     => 'rider',
            ]);
        });

        \App\Models\Rider::withoutEvents(function () use ($user, $request) {
            \App\Models\Rider::create([
                'user_id'      => $user->id,
                'phone'        => $request->phone,
                'vehicle_type' => $request->vehicle_type,
                'plate_number' => $request->plate_number,
                'is_available' => false,
            ]);
        });

        AuditLog::record(
            action:      'rider_created',
            description: "Admin created rider \"{$user->name}\".",
            model:       $user,
            newValues:   ['email' => $user->email, 'vehicle_type' => $request->vehicle_type],
        );

        return back()->with('success', "Rider {$user->name} created successfully.");
    }

    public function updateRider(Request $request, \App\Models\Rider $rider)
    {
        $request->validate([
            'phone'        => 'nullable|string|max:20',
            'vehicle_type' => 'required|in:motorcycle,bicycle',
            'plate_number' => 'nullable|string|max:20',
        ]);

        $rider->updateQuietly($request->only('phone', 'vehicle_type', 'plate_number'));
        $rider->refresh();

        AuditLog::record(
            action:      'rider_updated',
            description: "Admin updated rider \"{$rider->user->name}\".",
            model:       $rider,
        );

        return back()->with('success', 'Rider updated.');
    }

    public function removeRider(\App\Models\Rider $rider)
    {
        $riderName = $rider->user->name;
        $rider->user->updateQuietly(['role' => 'user']);
        $rider->deleteQuietly();

        AuditLog::record(
            action:      'rider_removed',
            description: "Admin removed rider \"{$riderName}\".",
        );

        return back()->with('success', 'Rider removed.');
    }

    // ════════════════════════════════════════════════════════
    // SETTINGS
    // ════════════════════════════════════════════════════════

    public function settings()
    {
        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);
        $isOpen         = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;

        return view('admin.settings', compact('isOpen', 'isOpenDelivery', 'isOpenPickup', 'isOpenDineIn'));
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

        $isOpenDelivery = $request->boolean('is_open_delivery', true);
        $isOpenPickup   = $request->boolean('is_open_pickup', true);
        $isOpenDineIn   = $request->boolean('is_open_dine_in', true);
        $isOpen         = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;

        cache()->forever('shop_is_open_delivery', $isOpenDelivery);
        cache()->forever('shop_is_open_pickup', $isOpenPickup);
        cache()->forever('shop_is_open_dine_in', $isOpenDineIn);
        cache()->forever('shop_is_open', $isOpen);

        cache()->forever('shop_settings', [
            'restaurant_name'  => $request->restaurant_name,
            'contact_email'    => $request->contact_email,
            'contact_phone'    => $request->contact_phone,
            'address'          => $request->address,
            'delivery_fee'     => $request->delivery_fee,
            'min_order'        => $request->min_order,
            'is_open_delivery' => $isOpenDelivery,
            'is_open_pickup'   => $isOpenPickup,
            'is_open_dine_in'  => $isOpenDineIn,
            'is_open'          => $isOpen,
        ]);

        broadcast(new \App\Events\ShopStatusUpdated($isOpen, $isOpenDelivery, $isOpenPickup, $isOpenDineIn));

        AuditLog::record(
            action:      'settings_changed',
            description: 'Restaurant settings updated.',
            newValues:   [
                'restaurant_name'  => $request->restaurant_name,
                'contact_email'    => $request->contact_email,
                'contact_phone'    => $request->contact_phone,
                'delivery_fee'     => $request->delivery_fee,
                'min_order'        => $request->min_order,
                'is_open_delivery' => $isOpenDelivery,
                'is_open_pickup'   => $isOpenPickup,
                'is_open_dine_in'  => $isOpenDineIn,
            ],
        );

        return back()->with('success', 'Restaurant settings saved successfully.');
    }

    // ── PATCH /admin/settings/toggle-open — quick open/close toggle per service ────────
    public function toggleOpen(Request $request)
    {
        $type = $request->input('type', 'all');

        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);

        if ($type === 'delivery') {
            $isOpenDelivery = !$isOpenDelivery;
            cache()->forever('shop_is_open_delivery', $isOpenDelivery);
        } elseif ($type === 'pickup') {
            $isOpenPickup = !$isOpenPickup;
            cache()->forever('shop_is_open_pickup', $isOpenPickup);
        } elseif ($type === 'dine_in') {
            $isOpenDineIn = !$isOpenDineIn;
            cache()->forever('shop_is_open_dine_in', $isOpenDineIn);
        } else {
            // Toggle overall shop
            $currentOverall = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;
            $newStatus = !$currentOverall;
            $isOpenDelivery = $newStatus;
            $isOpenPickup   = $newStatus;
            $isOpenDineIn   = $newStatus;
            cache()->forever('shop_is_open_delivery', $newStatus);
            cache()->forever('shop_is_open_pickup', $newStatus);
            cache()->forever('shop_is_open_dine_in', $newStatus);
        }

        $isOpen = $isOpenDelivery || $isOpenPickup || $isOpenDineIn;
        cache()->forever('shop_is_open', $isOpen);

        // Broadcast to all connected clients
        broadcast(new \App\Events\ShopStatusUpdated($isOpen, $isOpenDelivery, $isOpenPickup, $isOpenDineIn));

        AuditLog::record(
            action:      'settings_changed',
            description: "Shop open/close toggled ({$type}).",
            newValues:   [
                'toggle_type'      => $type,
                'is_open'          => $isOpen,
                'is_open_delivery' => $isOpenDelivery,
                'is_open_pickup'   => $isOpenPickup,
                'is_open_dine_in'  => $isOpenDineIn,
            ],
        );

        return response()->json([
            'success'          => true,
            'is_open'          => $isOpen,
            'is_open_delivery' => $isOpenDelivery,
            'is_open_pickup'   => $isOpenPickup,
            'is_open_dine_in'  => $isOpenDineIn,
            'message'          => 'Service status updated successfully.',
        ]);
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

        $user->update(['password' => $request->password]); // model cast handles hashing
        return back()->with('success', 'Password updated successfully.');
    }

    // ════════════════════════════════════════════════════════
    // AUDIT LOGS
    // ════════════════════════════════════════════════════════

    public function auditLogs(Request $request)
    {
        $query = \App\Models\AuditLog::latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('model')) {
            $query->where('auditable_type', 'like', '%' . $request->model . '%');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('auditable_label', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Distinct actions for the filter dropdown
        $actions = \App\Models\AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Distinct users for the filter dropdown
        $users = \App\Models\AuditLog::select('user_id', 'user_name')
            ->whereNotNull('user_id')
            ->distinct()
            ->orderBy('user_name')
            ->get();

        return view('admin.audit-logs', compact('logs', 'actions', 'users'));
    }

    // ── GET /admin/audit-logs/poll — new entries since last seen id ──────────
    public function auditLogsPoll(Request $request)
    {
        $afterId = (int) $request->input('after', 0);

        $entries = \App\Models\AuditLog::where('id', '>', $afterId)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn($log) => [
                'id'              => $log->id,
                'action'          => $log->action,
                'description'     => $log->description,
                'user_name'       => $log->user_name,
                'user_role'       => $log->user_role,
                'auditable_type'  => class_basename($log->auditable_type ?? ''),
                'auditable_label' => $log->auditable_label,
                'auditable_id'    => $log->auditable_id,
                'ip_address'      => $log->ip_address,
                'old_values'      => $log->old_values,
                'new_values'      => $log->new_values,
                'time'            => $log->created_at->format('g:i A'),
                'date'            => $log->created_at->format('M d, Y'),
                'ago'             => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'entries'    => $entries,
            'latest_id'  => $entries->max('id') ?? $afterId,
        ]);
    }

    /**
     * Update the quantity of a single item on an active order (increase or decrease).
     * A qty of 0 cancels the item entirely (delegates to cancelItem logic).
     *
     * Route: PATCH /admin/orders/{order}/items/{item}
     * Body:  { "qty": <new_absolute_quantity> }
     */
    public function updateItemQty(Request $request, \App\Models\Order $order, \App\Models\OrderItem $item)
    {
        if (!$order->isCancellable()) {
            return response()->json(['success' => false, 'message' => 'This order can no longer be modified.'], 422);
        }

        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false, 'message' => 'Item does not belong to this order.'], 422);
        }

        $newQty = (int) $request->input('qty');
        if ($newQty < 0) {
            return response()->json(['success' => false, 'message' => 'Quantity cannot be negative.'], 422);
        }

        // qty = 0 → full removal
        if ($newQty === 0) {
            return $this->cancelItem(new \Illuminate\Http\Request(), $order, $item);
        }

        $newSubtotal = round($item->unit_price * $newQty, 2);
        $item->updateQuietly(['quantity' => $newQty, 'subtotal' => $newSubtotal]);

        $order->load('items');
        $orderSubtotal = round($order->items->sum('subtotal'), 2);
        $order->updateQuietly([
            'subtotal' => $orderSubtotal,
            'total'    => $order->delivery_fee > 0 ? $orderSubtotal + $order->delivery_fee : $orderSubtotal,
        ]);
        $order->refresh();

        broadcast(new \App\Events\OrderStatusUpdated($order));

        AuditLog::record(
            action: 'order_item_qty_updated',
            description: "Admin set {$item->item_name} qty to {$newQty} on {$order->order_number}.",
            model: $order,
        );

        return response()->json([
            'success'   => true,
            'message'   => "{$item->item_name} updated to {$newQty}×.",
            'new_qty'   => $newQty,
            'items'     => $order->items->map(fn($i) => [
                'id'       => $i->id,
                'name'     => $i->item_name,
                'qty'      => $i->quantity,
                'price'    => (float) $i->unit_price,
                'subtotal' => (float) $i->subtotal,
                'modifiers'=> $i->modifiers ?? [],
            ])->values()->all(),
            'new_total' => (float) $order->total,
        ]);
    }

    /**
     * Remove (or reduce qty of) a single item from an active order.
     * Works for all order types (dine-in, pickup, delivery).
     * Mirrors ChefController@cancelItem but without the dine-in-only restriction.
     *
     * Route: DELETE /admin/orders/{order}/items/{item}
     */
    public function cancelItem(Request $request, \App\Models\Order $order, \App\Models\OrderItem $item)
    {
        if (!$order->isCancellable()) {
            return response()->json(['success' => false, 'message' => 'This order can no longer be modified.'], 422);
        }

        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false, 'message' => 'Item does not belong to this order.'], 422);
        }

        $cancelQty = (int) $request->input('qty', $item->quantity);
        $cancelQty = max(1, min($cancelQty, $item->quantity));

        if ($cancelQty < $item->quantity) {
            $newQty      = $item->quantity - $cancelQty;
            $newSubtotal = round($item->unit_price * $newQty, 2);
            $item->updateQuietly(['quantity' => $newQty, 'subtotal' => $newSubtotal]);

            $order->load('items');
            $orderSubtotal = round($order->items->sum('subtotal'), 2);
            $order->updateQuietly(['subtotal' => $orderSubtotal, 'total' => $order->delivery_fee > 0 ? $orderSubtotal + $order->delivery_fee : $orderSubtotal]);
            $order->refresh();

            broadcast(new \App\Events\OrderStatusUpdated($order));
            AuditLog::record(action: 'order_item_qty_reduced', description: "Admin reduced {$item->item_name} qty by {$cancelQty} on {$order->order_number}.", model: $order);

            return response()->json(['success' => true, 'message' => "{$cancelQty}× {$item->item_name} removed.", 'items_left' => $order->items->count()]);
        }

        $item->delete();
        $order->load('items');
        $remaining = $order->items;

        if ($remaining->isEmpty()) {
            $order->updateQuietly(['status' => 'cancelled', 'cancel_reason' => 'All items removed by admin', 'cancelled_at' => now(), 'subtotal' => 0, 'total' => 0]);
            $order->refresh();
            broadcast(new \App\Events\OrderStatusUpdated($order));
            AuditLog::record(action: 'order_cancelled', description: "Admin removed last item — {$order->order_number} cancelled.", model: $order);
            return response()->json(['success' => true, 'message' => "All items removed — order #{$order->order_number} cancelled.", 'items_left' => 0]);
        }

        $newSubtotal = round($remaining->sum('subtotal'), 2);
        $order->updateQuietly(['subtotal' => $newSubtotal, 'total' => $order->delivery_fee > 0 ? $newSubtotal + $order->delivery_fee : $newSubtotal]);
        $order->refresh();
        broadcast(new \App\Events\OrderStatusUpdated($order));
        AuditLog::record(action: 'order_item_removed', description: "Admin removed item from {$order->order_number}.", model: $order);

        return response()->json(['success' => true, 'message' => "Item removed from order #{$order->order_number}.", 'items_left' => $remaining->count()]);
    }
}
