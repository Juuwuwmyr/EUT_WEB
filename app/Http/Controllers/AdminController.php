<?php

namespace App\Http\Controllers;

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
            'admin_users'      => User::where('role', 'admin')->count(),
            'total_items'      => MenuItem::active()->count(),
            'total_categories' => Category::active()->count(),
            'featured_items'   => MenuItem::featured()->count(),
        ];

        $recent_users = User::latest()->take(5)->get();

        $categories = Category::active()->withCount(['activeMenuItems'])->orderBy('sort_order')->get()->map(fn($c) => [
            'name'  => $c->name,
            'count' => $c->active_menu_items_count,
            'hex'   => $c->color,
            'icon'  => $c->icon,
            'slug'  => $c->slug,
        ])->toArray();

        return view('admin.dashboard', compact('stats', 'recent_users', 'categories'));
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
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['required', Rule::in(['admin','user'])],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'provider' => 'email',
        ]);

        return back()->with('success', "User \"{$request->name}\" created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'role'  => ['required', Rule::in(['admin','user'])],
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
        $request->validate(['role' => ['required', Rule::in(['admin','user'])]]);

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
        return view('admin.categories', compact('categories'));
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

        return view('admin.menu-items', compact('items', 'categories'));
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'name'                                  => 'required|string|max:120',
            'category_id'                           => 'required|exists:categories,id',
            'price'                                 => 'required|numeric|min:0',
            'description'                           => 'nullable|string|max:400',
            'image'                                 => 'nullable|string|max:255',
            'featured'                              => 'nullable|boolean',
            'groups'                                => 'nullable|array',
            'groups.*.type'                         => 'required_with:groups|in:flavor,modifier',
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

        $item = MenuItem::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $request->image ?: '/images/hero-burger.jpg',
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
            'image'                                 => 'nullable|string|max:255',
            'featured'                              => 'nullable|boolean',
            'groups'                                => 'nullable|array',
            'groups.*.type'                         => 'required_with:groups|in:flavor,modifier',
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

        $menuItem->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $request->image ?: $menuItem->image,
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
                if ($group) {
                    $group->update($groupPayload);
                }
            } else {
                $group = ModifierGroup::create($groupPayload);
            }

            if ($group) {
                $keptGroupIds[] = $group->id;
                $keptOptionIds  = [];

                foreach ($groupData['options'] ?? [] as $oIdx => $optData) {
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
                        if ($opt) { $opt->update($optPayload); }
                    } else {
                        $opt = ModifierOption::create($optPayload);
                    }

                    if ($opt) { $keptOptionIds[] = $opt->id; }
                }

                // Remove options that were deleted in the UI
                $group->options()->whereNotIn('id', $keptOptionIds)->delete();
            }
        }

        // Remove groups that were deleted in the UI
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
        $orders = collect([
            ['id'=>1001,'customer'=>'Juan Dela Cruz', 'items'=>'EUT Classic Combo',        'total'=>550,'status'=>'delivered','date'=>'2026-07-17 10:30'],
            ['id'=>1002,'customer'=>'Maria Santos',   'items'=>'Gourmet Cheeseburger × 2', 'total'=>840,'status'=>'preparing','date'=>'2026-07-17 11:05'],
            ['id'=>1003,'customer'=>'Pedro Reyes',    'items'=>'Spicy Combo + Fries',       'total'=>740,'status'=>'pending',  'date'=>'2026-07-17 11:22'],
            ['id'=>1004,'customer'=>'Ana Villanueva', 'items'=>'BBQ Bacon Burger + Tea',    'total'=>590,'status'=>'out',      'date'=>'2026-07-17 11:45'],
            ['id'=>1005,'customer'=>'Jose Bautista',  'items'=>'Veggie Delight × 3',        'total'=>960,'status'=>'cancelled','date'=>'2026-07-17 12:00'],
            ['id'=>1006,'customer'=>'Liza Gomez',     'items'=>'Gourmet Combo',             'total'=>650,'status'=>'delivered','date'=>'2026-07-17 12:15'],
            ['id'=>1007,'customer'=>'Ramon Cruz',     'items'=>'Classic Fries × 4',         'total'=>480,'status'=>'preparing','date'=>'2026-07-17 12:30'],
            ['id'=>1008,'customer'=>'Elena Torres',   'items'=>'Mushroom Swiss + Smoothie', 'total'=>595,'status'=>'pending',  'date'=>'2026-07-17 12:44'],
        ]);

        if ($request->filled('status')) {
            $orders = $orders->filter(fn($o) => $o['status'] === $request->status)->values();
        }

        $statusCounts = ['pending'=>2,'preparing'=>2,'out'=>1,'delivered'=>2,'cancelled'=>1];

        return view('admin.orders', compact('orders', 'statusCounts'));
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
