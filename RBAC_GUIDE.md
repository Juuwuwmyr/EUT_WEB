# Role-Based Access Control (RBAC) System Guide

## Overview

The EUT_WEB application now has a comprehensive Role-Based Access Control (RBAC) system that allows administrators to manage what users and roles can access and modify in the system.

## Features

✅ **Role-Based Permissions** - Assign permissions to entire roles (admin, chef, rider, user)
✅ **User-Specific Overrides** - Grant or revoke specific permissions for individual users
✅ **56 Default Permissions** - Covering all major features (dashboard, users, categories, menu, orders, kitchen, riders, delivery, settings, QR codes, audit logs, permissions)
✅ **Permission Groups** - Permissions organized by feature for easy management
✅ **Admin UI** - Easy-to-use interface for managing permissions
✅ **Middleware Protection** - All routes protected by permission checks
✅ **Caching** - User permissions are cached for performance

## Installation & Setup

### 1. Run Migrations

```bash
php artisan migrate
```

This creates three tables:
- `permissions` - Stores all available permissions
- `role_permissions` - Maps permissions to roles
- `user_permissions` - Stores user-specific permission overrides

### 2. Seed Default Permissions

```bash
php artisan db:seed --class=PermissionsSeeder
```

This will:
- Create 56 default permissions grouped by features
- Assign appropriate permissions to each role:
  - **Admin**: Full access to all features
  - **Chef**: Kitchen operations, orders, printing
  - **Rider**: Delivery operations, location updates
  - **User**: No admin permissions

## Usage

### Accessing the Permissions Manager

1. Log in as an admin
2. Navigate to **Admin Panel** → **Permissions**
3. You'll see three tabs:
   - **Role Permissions** - Manage permissions for each role
   - **All Permissions** - View, create, edit, delete permissions
   - **User Overrides** - Manage user-specific permissions

### Managing Role Permissions

1. Go to the **Role Permissions** tab
2. Select a role from the dropdown (admin, chef, rider, user)
3. Check/uncheck permissions for that role
4. Click **Save Role Permissions**

All users with that role will inherit these permissions.

### Managing User-Specific Permissions

User-specific permissions override role permissions. You can:
- **Grant** a permission to a user even if their role doesn't have it
- **Revoke** a permission from a user even if their role has it
- **Reset** to remove the override and use the role's default

To manage user permissions:
1. Go to **Admin Panel** → **Users**
2. Click on a user
3. Manage their specific permissions

### Creating Custom Permissions

1. Go to **All Permissions** tab
2. Click **New Permission**
3. Fill in:
   - **Name**: Human-readable name (e.g., "Manage Orders")
   - **Slug**: Unique identifier (auto-generated, e.g., "manage_orders")
   - **Group**: Feature category (e.g., "orders")
   - **Description**: Brief explanation
4. Click **Save Permission**
5. Assign it to roles as needed

## Permission System in Code

### Checking Permissions in Controllers

```php
// Check if user has permission
if (auth()->user()->hasPermission('edit_menu_items')) {
    // Allow action
}

// Check if user has any of multiple permissions
if (auth()->user()->hasAnyPermission(['edit_menu_items', 'create_menu_items'])) {
    // Allow action
}

// Check if user has all permissions
if (auth()->user()->hasAllPermissions(['edit_menu_items', 'delete_menu_items'])) {
    // Allow action
}

// Alternative syntax
if (auth()->user()->can('edit_menu_items')) {
    // Allow action
}
```

### Checking Permissions in Blade Templates

```blade
@if(auth()->user()->hasPermission('edit_menu_items'))
    <button>Edit Item</button>
@endif

{{-- Alternative syntax --}}
@if(auth()->user()->can('edit_menu_items'))
    <button>Edit Item</button>
@endif
```

### Using Permission Middleware in Routes

```php
// Single permission
Route::get('/admin/users', [AdminController::class, 'users'])
    ->middleware('permission:view_users');

// Multiple permissions (all required)
Route::post('/admin/users', [AdminController::class, 'store'])
    ->middleware('permission:create_users,edit_users');

// Multiple permissions (any required)
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('permission:view_dashboard,view_analytics,any');
```

### Granting/Revoking Permissions Programmatically

```php
$user = User::find(1);

// Grant a permission
$user->grantPermission('edit_menu_items');

// Revoke a permission
$user->revokePermission('edit_menu_items');

// Remove override (revert to role default)
$user->removePermissionOverride('edit_menu_items');

// Clear permission cache
$user->clearPermissionCache();
```

## Default Permissions by Group

### Dashboard
- `view_dashboard` - Access the admin dashboard
- `view_analytics` - View revenue and sales analytics

### Users Management
- `view_users` - View list of users
- `create_users` - Create new users
- `edit_users` - Edit existing users
- `delete_users` - Delete users
- `change_user_roles` - Change user roles

### Categories Management
- `view_categories` - View menu categories
- `create_categories` - Create new categories
- `edit_categories` - Edit existing categories
- `delete_categories` - Delete categories

### Menu Items Management
- `view_menu_items` - View menu items
- `create_menu_items` - Create new menu items
- `edit_menu_items` - Edit existing menu items
- `delete_menu_items` - Delete menu items
- `manage_modifiers` - Manage modifiers and add-ons

### Orders Management
- `view_orders` - View all orders
- `accept_orders` - Accept pending orders
- `update_order_status` - Update order status
- `assign_riders` - Assign riders to delivery orders
- `cancel_orders` - Cancel orders
- `delete_orders` - Permanently delete orders
- `complete_table_orders` - Mark dine-in table orders as complete
- `view_order_details` - View full order details

### Kitchen Management
- `view_kitchen_orders` - Access kitchen dashboard
- `start_cooking` - Mark orders as being prepared
- `mark_order_ready` - Mark orders as ready for pickup
- `cancel_order_items` - Cancel individual items
- `print_receipts` - Print receipts and kitchen tickets

### Riders Management
- `view_riders` - View list of riders
- `create_riders` - Add new riders
- `edit_riders` - Edit rider information
- `delete_riders` - Remove riders
- `view_rider_locations` - View real-time rider locations

### Delivery Management
- `view_deliveries` - View assigned deliveries
- `update_delivery_status` - Update delivery status
- `mark_delivered` - Mark orders as delivered
- `update_location` - Update rider location

### Settings
- `view_settings` - View system settings
- `edit_settings` - Modify system settings
- `toggle_shop_status` - Open/close shop for orders
- `manage_delivery_fees` - Configure delivery fees

### QR Codes
- `view_qr_codes` - View table QR codes
- `generate_qr_codes` - Generate and print QR codes

### Audit Logs
- `view_audit_logs` - View system audit logs

### Permissions Management
- `view_permissions` - View permissions list
- `manage_permissions` - Create, edit, delete permissions
- `manage_role_permissions` - Assign permissions to roles
- `manage_user_permissions` - Override individual user permissions

## Default Role Permissions

### Admin Role
Has **all** permissions - full system access

### Chef Role
- Kitchen operations (view, start, mark ready, cancel items)
- Print receipts
- Assign riders
- View riders

### Rider Role
- View deliveries
- Update delivery status
- Mark orders as delivered
- Update location

### User Role
- No admin permissions (customer access only)

## Super Admin

You can designate a super admin by setting the `SUPER_ADMIN_EMAIL` environment variable:

```env
SUPER_ADMIN_EMAIL=admin@example.com
```

The super admin bypasses all permission checks.

## Performance Considerations

- User permissions are cached for 1 hour to improve performance
- Cache is automatically cleared when:
  - User permissions are granted/revoked
  - Role permissions are updated for the user's role
  - User's role is changed

## Security Best Practices

1. **Principle of Least Privilege** - Only grant necessary permissions
2. **Regular Audits** - Review permissions regularly using the audit logs
3. **Role-Based First** - Use role permissions as default, user overrides only when needed
4. **Protect Critical Operations** - Always require verification for sensitive operations
5. **Monitor Changes** - All permission changes are logged in audit logs

## Troubleshooting

### Permission not working
1. Check if the permission exists in the database
2. Verify the permission is assigned to the user's role
3. Clear the user's permission cache
4. Check if there's a user-specific override

### Cache issues
```php
// Clear specific user's permission cache
$user->clearPermissionCache();

// Or clear all cache
php artisan cache:clear
```

### Missing permissions after seeding
Run the seeder again:
```bash
php artisan db:seed --class=PermissionsSeeder
```

## Database Schema

### permissions table
- `id` - Primary key
- `name` - Human-readable name
- `slug` - Unique identifier
- `description` - Brief explanation
- `group` - Feature category
- `is_active` - Active status
- `created_at`, `updated_at`

### role_permissions table
- `id` - Primary key
- `role` - Role name (admin, chef, rider, user)
- `permission_id` - Foreign key to permissions
- `created_at`, `updated_at`
- Unique constraint on (role, permission_id)

### user_permissions table
- `id` - Primary key
- `user_id` - Foreign key to users
- `permission_id` - Foreign key to permissions
- `granted` - Boolean (true = grant, false = revoke)
- `created_at`, `updated_at`
- Unique constraint on (user_id, permission_id)

## API Endpoints

All permission management endpoints are under `/admin/permissions`:

- `GET /admin/permissions` - View permissions page
- `POST /admin/permissions` - Create permission
- `PUT /admin/permissions/{id}` - Update permission
- `DELETE /admin/permissions/{id}` - Delete permission
- `PATCH /admin/permissions/{id}/toggle-active` - Toggle active status
- `POST /admin/permissions/role-permissions` - Update role permissions
- `GET /admin/permissions/users/{user}` - View user permissions
- `POST /admin/permissions/users/{user}/grant` - Grant user permission
- `POST /admin/permissions/users/{user}/revoke` - Revoke user permission
- `POST /admin/permissions/users/{user}/reset` - Reset to role default

## Example Use Cases

### Limiting Menu Access
Only allow senior staff to edit menu items:
1. Remove `edit_menu_items` from chef role
2. Grant `edit_menu_items` to specific senior chef users

### Temporary Access
Grant temporary rider access to view analytics:
1. Grant `view_analytics` permission to specific rider user
2. Remove when no longer needed

### Custom Admin Roles
Create a "Manager" user with limited admin access:
1. Create user with 'user' role
2. Grant specific permissions: `view_orders`, `view_analytics`, `view_users`

## Support

For issues or questions about the RBAC system:
1. Check this guide
2. Review audit logs for permission changes
3. Test permissions in a non-production environment first

---

**Last Updated:** August 13, 2026
**Version:** 1.0.0
