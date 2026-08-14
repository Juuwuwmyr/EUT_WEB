<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission_id',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the user that owns this permission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the permission
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Grant a permission to a user
     */
    public static function grant(int $userId, int $permissionId): void
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'permission_id' => $permissionId],
            ['granted' => true]
        );
    }

    /**
     * Revoke a permission from a user
     */
    public static function revoke(int $userId, int $permissionId): void
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'permission_id' => $permissionId],
            ['granted' => false]
        );
    }

    /**
     * Remove a user's explicit permission (revert to role default)
     */
    public static function remove(int $userId, int $permissionId): void
    {
        static::where('user_id', $userId)
            ->where('permission_id', $permissionId)
            ->delete();
    }
}
