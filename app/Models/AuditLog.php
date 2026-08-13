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
