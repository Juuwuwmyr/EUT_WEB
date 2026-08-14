<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all system permissions grouped by feature
        $permissions = [
            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'view_dashboard',
                'description' => 'Access the admin dashboard',
                'group' => 'dashboard',
            ],
            [
                'name' => 'View Analytics',
                'slug' => 'view_analytics',
                'description' => 'View revenue and sales analytics',
                'group' => 'dashboard',
            ],

            // Users Management
            [
                'name' => 'View Users',
                'slug' => 'view_users',
                'description' => 'View list of users',
                'group' => 'users',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create_users',
                'description' => 'Create new users',
                'group' => 'users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'edit_users',
                'description' => 'Edit existing users',
                'group' => 'users',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete_users',
                'description' => 'Delete users',
                'group' => 'users',
            ],
            [
                'name' => 'Change User Roles',
                'slug' => 'change_user_roles',
                'description' => 'Change user roles',
                'group' => 'users',
            ],

            // Categories Management
            [
                'name' => 'View Categories',
                'slug' => 'view_categories',
                'description' => 'View menu categories',
                'group' => 'categories',
            ],
            [
                'name' => 'Create Categories',
                'slug' => 'create_categories',
                'description' => 'Create new categories',
                'group' => 'categories',
            ],
            [
                'name' => 'Edit Categories',
                'slug' => 'edit_categories',
                'description' => 'Edit existing categories',
                'group' => 'categories',
            ],
            [
                'name' => 'Delete Categories',
                'slug' => 'delete_categories',
                'description' => 'Delete categories',
                'group' => 'categories',
            ],

            // Menu Items Management
            [
                'name' => 'View Menu Items',
                'slug' => 'view_menu_items',
                'description' => 'View menu items',
                'group' => 'menu',
            ],
            [
                'name' => 'Create Menu Items',
                'slug' => 'create_menu_items',
                'description' => 'Create new menu items',
                'group' => 'menu',
            ],
            [
                'name' => 'Edit Menu Items',
                'slug' => 'edit_menu_items',
                'description' => 'Edit existing menu items',
                'group' => 'menu',
            ],
            [
                'name' => 'Delete Menu Items',
                'slug' => 'delete_menu_items',
                'description' => 'Delete menu items',
                'group' => 'menu',
            ],
            [
                'name' => 'Manage Modifiers',
                'slug' => 'manage_modifiers',
                'description' => 'Manage modifiers and add-ons',
                'group' => 'menu',
            ],

            // Orders Management
            [
                'name' => 'View Orders',
                'slug' => 'view_orders',
                'description' => 'View all orders',
                'group' => 'orders',
            ],
            [
                'name' => 'Accept Orders',
                'slug' => 'accept_orders',
                'description' => 'Accept pending orders',
                'group' => 'orders',
            ],
            [
                'name' => 'Update Order Status',
                'slug' => 'update_order_status',
                'description' => 'Update order status',
                'group' => 'orders',
            ],
            [
                'name' => 'Assign Riders',
                'slug' => 'assign_riders',
                'description' => 'Assign riders to delivery orders',
                'group' => 'orders',
            ],
            [
                'name' => 'Cancel Orders',
                'slug' => 'cancel_orders',
                'description' => 'Cancel orders',
                'group' => 'orders',
            ],
            [
                'name' => 'Delete Orders',
                'slug' => 'delete_orders',
                'description' => 'Permanently delete orders',
                'group' => 'orders',
            ],
            [
                'name' => 'Complete Table Orders',
                'slug' => 'complete_table_orders',
                'description' => 'Mark dine-in table orders as complete',
                'group' => 'orders',
            ],
            [
                'name' => 'View Order Details',
                'slug' => 'view_order_details',
                'description' => 'View full order details and customer info',
                'group' => 'orders',
            ],

            // Kitchen Management
            [
                'name' => 'View Kitchen Orders',
                'slug' => 'view_kitchen_orders',
                'description' => 'Access kitchen dashboard',
                'group' => 'kitchen',
            ],
            [
                'name' => 'Start Cooking',
                'slug' => 'start_cooking',
                'description' => 'Mark orders as being prepared',
                'group' => 'kitchen',
            ],
            [
                'name' => 'Mark Order Ready',
                'slug' => 'mark_order_ready',
                'description' => 'Mark orders as ready for pickup',
                'group' => 'kitchen',
            ],
            [
                'name' => 'Cancel Order Items',
                'slug' => 'cancel_order_items',
                'description' => 'Cancel individual items in an order',
                'group' => 'kitchen',
            ],
            [
                'name' => 'Print Receipts',
                'slug' => 'print_receipts',
                'description' => 'Print order receipts and kitchen tickets',
                'group' => 'kitchen',
            ],

            // Riders Management
            [
                'name' => 'View Riders',
                'slug' => 'view_riders',
                'description' => 'View list of riders',
                'group' => 'riders',
            ],
            [
                'name' => 'Create Riders',
                'slug' => 'create_riders',
                'description' => 'Add new riders',
                'group' => 'riders',
            ],
            [
                'name' => 'Edit Riders',
                'slug' => 'edit_riders',
                'description' => 'Edit rider information',
                'group' => 'riders',
            ],
            [
                'name' => 'Delete Riders',
                'slug' => 'delete_riders',
                'description' => 'Remove riders',
                'group' => 'riders',
            ],
            [
                'name' => 'View Rider Locations',
                'slug' => 'view_rider_locations',
                'description' => 'View real-time rider locations on map',
                'group' => 'riders',
            ],

            // Delivery Management
            [
                'name' => 'View Deliveries',
                'slug' => 'view_deliveries',
                'description' => 'View assigned deliveries',
                'group' => 'delivery',
            ],
            [
                'name' => 'Update Delivery Status',
                'slug' => 'update_delivery_status',
                'description' => 'Update delivery status',
                'group' => 'delivery',
            ],
            [
                'name' => 'Mark Delivered',
                'slug' => 'mark_delivered',
                'description' => 'Mark orders as delivered',
                'group' => 'delivery',
            ],
            [
                'name' => 'Update Location',
                'slug' => 'update_location',
                'description' => 'Update rider location',
                'group' => 'delivery',
            ],

            // Settings
            [
                'name' => 'View Settings',
                'slug' => 'view_settings',
                'description' => 'View system settings',
                'group' => 'settings',
            ],
            [
                'name' => 'Edit Settings',
                'slug' => 'edit_settings',
                'description' => 'Modify system settings',
                'group' => 'settings',
            ],
            [
                'name' => 'Toggle Shop Status',
                'slug' => 'toggle_shop_status',
                'description' => 'Open/close shop for orders',
                'group' => 'settings',
            ],
            [
                'name' => 'Manage Delivery Fees',
                'slug' => 'manage_delivery_fees',
                'description' => 'Configure delivery fees',
                'group' => 'settings',
            ],

            // QR Codes
            [
                'name' => 'View QR Codes',
                'slug' => 'view_qr_codes',
                'description' => 'View table QR codes',
                'group' => 'qr_codes',
            ],
            [
                'name' => 'Generate QR Codes',
                'slug' => 'generate_qr_codes',
                'description' => 'Generate and print QR codes',
                'group' => 'qr_codes',
            ],

            // Audit Logs
            [
                'name' => 'View Audit Logs',
                'slug' => 'view_audit_logs',
                'description' => 'View system audit logs',
                'group' => 'audit',
            ],

            // Permissions Management
            [
                'name' => 'View Permissions',
                'slug' => 'view_permissions',
                'description' => 'View permissions list',
                'group' => 'permissions',
            ],
            [
                'name' => 'Manage Permissions',
                'slug' => 'manage_permissions',
                'description' => 'Create, edit, and delete permissions',
                'group' => 'permissions',
            ],
            [
                'name' => 'Manage Role Permissions',
                'slug' => 'manage_role_permissions',
                'description' => 'Assign permissions to roles',
                'group' => 'permissions',
            ],
            [
                'name' => 'Manage User Permissions',
                'slug' => 'manage_user_permissions',
                'description' => 'Override individual user permissions',
                'group' => 'permissions',
            ],
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Define default role permissions
        $rolePermissions = [
            'admin' => [
                // Dashboard
                'view_dashboard',
                'view_analytics',
                // Users
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'change_user_roles',
                // Categories
                'view_categories',
                'create_categories',
                'edit_categories',
                'delete_categories',
                // Menu
                'view_menu_items',
                'create_menu_items',
                'edit_menu_items',
                'delete_menu_items',
                'manage_modifiers',
                // Orders
                'view_orders',
                'accept_orders',
                'update_order_status',
                'assign_riders',
                'cancel_orders',
                'delete_orders',
                'complete_table_orders',
                'view_order_details',
                // Kitchen
                'view_kitchen_orders',
                'start_cooking',
                'mark_order_ready',
                'cancel_order_items',
                'print_receipts',
                // Riders
                'view_riders',
                'create_riders',
                'edit_riders',
                'delete_riders',
                'view_rider_locations',
                // Settings
                'view_settings',
                'edit_settings',
                'toggle_shop_status',
                'manage_delivery_fees',
                // QR Codes
                'view_qr_codes',
                'generate_qr_codes',
                // Audit
                'view_audit_logs',
                // Permissions
                'view_permissions',
                'manage_permissions',
                'manage_role_permissions',
                'manage_user_permissions',
            ],
            'chef' => [
                'view_kitchen_orders',
                'accept_orders',
                'start_cooking',
                'mark_order_ready',
                'cancel_order_items',
                'print_receipts',
                'assign_riders',
                'view_riders',
            ],
            'rider' => [
                'view_deliveries',
                'update_delivery_status',
                'mark_delivered',
                'update_location',
            ],
            'user' => [
                // Regular users have no admin permissions
            ],
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $role => $permissionSlugs) {
            foreach ($permissionSlugs as $slug) {
                $permission = Permission::where('slug', $slug)->first();
                if ($permission) {
                    RolePermission::firstOrCreate([
                        'role' => $role,
                        'permission_id' => $permission->id,
                    ]);
                }
            }
        }

        $this->command->info('✓ Permissions seeded successfully!');
        $this->command->info('  - ' . count($permissions) . ' permissions created');
        $this->command->info('  - Role permissions assigned for: ' . implode(', ', array_keys($rolePermissions)));
    }
}
