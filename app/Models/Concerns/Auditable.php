<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

/**
 * Attach this trait to any Eloquent model to get automatic
 * create / update / delete audit entries.
 *
 * Usage:  use \App\Models\Concerns\Auditable;
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        // ── CREATED ───────────────────────────────────────
        static::created(function ($model) {
            AuditLog::record(
                action:      'created',
                description: class_basename($model) . ' created.',
                model:       $model,
                newValues:   AuditLog::sanitize($model->getAttributes()),
            );
        });

        // ── UPDATED ───────────────────────────────────────
        static::updated(function ($model) {
            $dirty = $model->getDirty();

            // Strip noisy fields that change automatically and have no audit value
            $ignored = [
                'updated_at', 'created_at',
                'email_verified_at', 'remember_token',
                'email_verification_code', 'email_verification_code_expires_at',
                'last_login_at', 'last_seen_at',
                // High-frequency / low-value fields
                'current_lat', 'current_lng',
                'pickup_slip_printed_at', 'total_deliveries',
                'ordering_locked', 'cash_received', 'change_due',
            ];
            $changed = array_diff_key($dirty, array_flip($ignored));
            if (empty($changed)) {
                return;
            }

            $old = array_intersect_key(
                AuditLog::sanitize($model->getOriginal()),
                $changed
            );
            $new = AuditLog::sanitize($changed);

            // Derive a friendlier action label for common single-field changes
            $action = 'updated';
            if (count($changed) === 1) {
                $field = array_key_first($changed);
                $action = match ($field) {
                    'status'      => 'status_changed',
                    'is_archived' => $changed[$field] ? 'archived' : 'restored',
                    'role'        => $changed[$field] === 'archived' ? 'archived' : 'role_changed',
                    default       => 'updated',
                };
            }

            AuditLog::record(
                action:      $action,
                description: class_basename($model) . ' ' . $action . '.',
                model:       $model,
                oldValues:   $old,
                newValues:   $new,
            );
        });

        // ── DELETED ───────────────────────────────────────
        static::deleted(function ($model) {
            AuditLog::record(
                action:      'deleted',
                description: class_basename($model) . ' deleted.',
                model:       $model,
                oldValues:   AuditLog::sanitize($model->getAttributes()),
            );
        });
    }
}
