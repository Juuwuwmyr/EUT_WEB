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

            // Nothing meaningful changed (e.g. only updated_at bumped)
            $ignored = ['updated_at'];
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
