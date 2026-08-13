<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper — the main entry point ──────────────

    /**
     * Write one audit record.
     *
     * @param  string       $action     e.g. 'created', 'updated', 'deleted'
     * @param  string|null  $description  Human-readable sentence
     * @param  Model|null   $model      The affected Eloquent model instance
     * @param  array|null   $oldValues  Attributes before the change
     * @param  array|null   $newValues  Attributes after the change
     */
    public static function record(
        string  $action,
        ?string $description = null,
        ?Model  $model       = null,
        ?array  $oldValues   = null,
        ?array  $newValues   = null,
    ): void {
        // Skip during Artisan / queue workers to avoid noise
        if (app()->runningInConsole()) {
            return;
        }

        $user = auth()->user();

        static::create([
            'user_id'         => $user?->id,
            'user_name'       => $user?->name,
            'user_role'       => $user?->role,
            'action'          => $action,
            'description'     => $description,
            'auditable_type'  => $model ? get_class($model) : null,
            'auditable_id'    => $model?->getKey(),
            'auditable_label' => $model ? static::labelFor($model) : null,
            'old_values'      => $oldValues,
            'new_values'      => $newValues,
            'ip_address'      => request()->ip(),
            'user_agent'      => substr((string) request()->userAgent(), 0, 500),
            'url'             => substr((string) request()->fullUrl(), 0, 500),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * UI colors/icons for an action badge (fg, bg, lucide icon name).
     */
    public static function actionMeta(string $action): array
    {
        return match ($action) {
            'created', 'restored', 'order_accepted', 'order_delivered', 'rider_created'
                => ['#10b981', 'rgba(16,185,129,.12)', 'plus-circle'],
            'updated', 'profile_updated', 'rider_updated'
                => ['#3b82f6', 'rgba(59,130,246,.12)', 'pencil'],
            'deleted', 'user_deleted', 'category_deleted', 'order_deleted', 'order_cancelled'
                => ['#ef4444', 'rgba(239,68,68,.12)', 'trash-2'],
            'archived', 'user_archived'
                => ['#f59e0b', 'rgba(245,158,11,.12)', 'archive'],
            'status_changed', 'order_status_updated', 'order_picked_up', 'order_cooking_started',
            'order_marked_ready', 'rider_status_changed', 'rider_assigned'
                => ['#8b5cf6', 'rgba(139,92,246,.12)', 'refresh-cw'],
            'role_changed'
                => ['#f59e0b', 'rgba(245,158,11,.12)', 'shield'],
            'settings_changed'
                => ['#ec4899', 'rgba(236,72,153,.12)', 'settings'],
            'login'
                => ['#6366f1', 'rgba(99,102,241,.12)', 'log-in'],
            'signup'
                => ['#6366f1', 'rgba(99,102,241,.12)', 'user-plus'],
            'logout'
                => ['#6b7280', 'rgba(107,114,128,.1)', 'log-out'],
            'password_changed'
                => ['#f97316', 'rgba(249,115,22,.12)', 'key'],
            'order_item_removed', 'pickup_slip_printed'
                => ['#64748b', 'rgba(100,116,139,.12)', 'file-text'],
            'rider_removed'
                => ['#ef4444', 'rgba(239,68,68,.12)', 'user-x'],
            default
                => ['#a3a3a3', 'rgba(163,163,163,.08)', 'activity'],
        };
    }

    /**
     * Action badge colors for the audit log UI (fg + bg only).
     */
    public static function actionMetaForJs(): array
    {
        $actions = [
            'created', 'updated', 'deleted', 'archived', 'restored', 'status_changed',
            'role_changed', 'settings_changed', 'login', 'logout', 'password_changed',
            'profile_updated', 'user_archived', 'user_deleted', 'category_deleted',
            'signup',
            'order_accepted', 'order_status_updated', 'order_deleted', 'order_cancelled',
            'order_picked_up', 'order_delivered', 'order_cooking_started', 'order_marked_ready',
            'order_item_removed', 'pickup_slip_printed', 'rider_assigned', 'rider_created',
            'rider_updated', 'rider_removed', 'rider_status_changed',
        ];

        return collect($actions)
            ->mapWithKeys(fn (string $action) => [$action => array_slice(static::actionMeta($action), 0, 2)])
            ->all();
    }

    /**
     * Derive a human-readable label for any model instance.
     * Add more cases as needed.
     */
    private static function labelFor(Model $model): string
    {
        return match (true) {
            method_exists($model, 'getOrderNumberAttribute') => $model->order_number,  // Order
            isset($model->name)  => $model->name,
            isset($model->title) => $model->title,
            isset($model->email) => $model->email,
            default              => class_basename($model) . '#' . $model->getKey(),
        };
    }

    /**
     * Strip sensitive fields so they never appear in audit logs.
     */
    public static function sanitize(array $attrs): array
    {
        $hidden = ['password', 'remember_token', 'google_id', 'api_token'];
        foreach ($hidden as $key) {
            if (array_key_exists($key, $attrs)) {
                $attrs[$key] = '***';
            }
        }
        return $attrs;
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForModel($query, string $type, int $id)
    {
        return $query->where('auditable_type', $type)->where('auditable_id', $id);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
