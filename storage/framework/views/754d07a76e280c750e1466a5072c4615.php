<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(99,102,241,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="users-round" style="width:1.2rem;height:1.2rem;color:#6366f1;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Users</h1>
            <p style="margin:0;">Manage registered accounts and assign roles.</p>
        </div>
    </div>
    <button onclick="openModal('addUserModal')" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
        <i data-lucide="user-plus" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i> Add User
    </button>
</div>

<div class="section-card mb-6">
    
    <form method="GET" action="<?php echo e(route('admin.users')); ?>" class="filter-bar">
        <div style="position:relative;max-width:280px;width:100%;">
            <i data-lucide="search" style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);width:.9rem;height:.9rem;color:var(--text-muted);stroke-width:2;pointer-events:none;"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name or email…" class="admin-input" style="padding-left:2.1rem;">
        </div>
        <select name="role" class="admin-input" style="max-width:140px;">
            <option value="">All Roles</option>
            <option value="user"     <?php echo e(request('role')==='user'     ? 'selected':''); ?>>User</option>
            <option value="admin"    <?php echo e(request('role')==='admin'    ? 'selected':''); ?>>Admin</option>
            <option value="archived" <?php echo e(request('role')==='archived' ? 'selected':''); ?>>Archived</option>
        </select>
        <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.35rem;">
            <i data-lucide="filter" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Filter
        </button>
        <?php if(request('search') || request('role')): ?>
            <a href="<?php echo e(route('admin.users')); ?>" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.35rem;">
                <i data-lucide="x" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Clear
            </a>
        <?php endif; ?>
        <span style="margin-left:auto;font-size:.8rem;color:var(--text-muted);"><?php echo e($users->total()); ?> total</span>
    </form>

    
    <table class="admin-table">
        <thead>
            <tr><th>#</th><th>User</th><th>Email</th><th>Provider</th><th>Role</th><th>Joined</th><th style="text-align:center;">Actions</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="color:var(--text-muted);font-size:.7rem;font-family:monospace;"><?php echo e($user->id); ?></td>
                <td>
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        <?php if($user->avatar): ?>
                            <img src="<?php echo e($user->avatar); ?>" style="width:2.25rem;height:2.25rem;border-radius:50%;object-fit:cover;border:2px solid var(--border-card);" alt="">
                        <?php else: ?>
                            <div style="width:2.25rem;height:2.25rem;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                <?php echo e(strtoupper(substr($user->name,0,1))); ?>

                            </div>
                        <?php endif; ?>
                        <div>
                            <span style="font-weight:600;color:var(--text-strong);display:block;line-height:1.2;"><?php echo e($user->name); ?></span>
                            <?php if($user->id === auth()->id()): ?>
                                <span style="font-size:.65rem;color:#d97706;">(you)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:.8rem;"><?php echo e($user->email); ?></td>
                <td>
                    <?php if($user->provider === 'google'): ?>
                        <span style="font-size:.75rem;color:#2563eb;display:inline-flex;align-items:center;gap:.25rem;">
                            <svg style="width:.875rem;height:.875rem;" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                            Google
                        </span>
                    <?php else: ?>
                        <span style="font-size:.75rem;color:var(--text-muted);">Email</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?php echo e($user->role==='admin' ? 'badge-admin' : ($user->role==='archived' ? 'badge-cancelled' : 'badge-user')); ?>">
                        <?php echo e(ucfirst($user->role ?? 'user')); ?>

                    </span>
                </td>
                <td style="color:var(--text-muted);font-size:.75rem;"><?php echo e($user->created_at->format('M d, Y')); ?></td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:center;gap:.375rem;">
                        
                        <button onclick='openEditUser(<?php echo json_encode($user, 15, 512) ?>)' class="btn-icon-edit" title="Edit">
                            <i data-lucide="pencil" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                        </button>
                        
                        <?php if($user->id !== auth()->id()): ?>
                        <form method="POST" action="<?php echo e(route('admin.users.archive', $user)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="<?php echo e($user->role==='archived' ? 'btn-icon-restore' : 'btn-icon-archive'); ?>"
                                    title="<?php echo e($user->role==='archived' ? 'Restore' : 'Archive'); ?>"
                                    onclick="return confirm('<?php echo e($user->role==='archived' ? 'Restore' : 'Archive'); ?> <?php echo e(addslashes($user->name)); ?>?')">
                                <i data-lucide="<?php echo e($user->role==='archived' ? 'archive-restore' : 'archive'); ?>" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                        
                        <form method="POST" action="<?php echo e(route('admin.users.delete', $user)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-icon-delete" title="Delete"
                                    onclick="return confirm('Permanently delete <?php echo e(addslashes($user->name)); ?>? This cannot be undone.')">
                                <i data-lucide="trash-2" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:3rem;">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($users->hasPages()): ?>
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border-divider);display:flex;justify-content:space-between;align-items:center;font-size:.875rem;color:var(--text-muted);">
        <span>Showing <?php echo e($users->firstItem()); ?>–<?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?></span>
        <div style="display:flex;gap:.375rem;">
            <?php if($users->onFirstPage()): ?>
                <span class="btn-ghost" style="opacity:.4;cursor:not-allowed;">← Prev</span>
            <?php else: ?>
                <a href="<?php echo e($users->previousPageUrl()); ?>" class="btn-ghost">← Prev</a>
            <?php endif; ?>
            <?php if($users->hasMorePages()): ?>
                <a href="<?php echo e($users->nextPageUrl()); ?>" class="btn-ghost">Next →</a>
            <?php else: ?>
                <span class="btn-ghost" style="opacity:.4;cursor:not-allowed;">Next →</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<div id="addUserModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'addUserModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="user-plus" style="width:1.1rem;height:1.1rem;color:#6366f1;stroke-width:2;"></i>
                <h3 class="modal-title">Add New User</h3>
            </div>
            <button onclick="closeModal('addUserModal')" class="modal-close"><i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i></button>
        </div>
        <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">Full Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" class="admin-input" placeholder="Juan Dela Cruz" required>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Email Address <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" class="admin-input" placeholder="juan@example.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">Password <span style="color:#dc2626;">*</span></label>
                        <input type="password" name="password" class="admin-input" placeholder="Min. 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Role <span style="color:#dc2626;">*</span></label>
                        <select name="role" class="admin-input" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addUserModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                    <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>


<div id="editUserModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'editUserModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="user-pen" style="width:1.1rem;height:1.1rem;color:#6366f1;stroke-width:2;"></i>
                <h3 class="modal-title">Edit User</h3>
            </div>
            <button onclick="closeModal('editUserModal')" class="modal-close"><i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i></button>
        </div>
        <form method="POST" id="editUserForm">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">Full Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" id="editUserName" class="admin-input" required>
                    </div>
                    <div class="form-group">
                        <label class="field-label">Email Address <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" id="editUserEmail" class="admin-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="field-label">New Password <span style="color:var(--text-muted);font-weight:400;text-transform:none;">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="admin-input" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="field-label">Role <span style="color:#dc2626;">*</span></label>
                        <select name="role" id="editUserRole" class="admin-input" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editUserModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                    <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditUser(user) {
    document.getElementById('editUserName').value  = user.name;
    document.getElementById('editUserEmail').value = user.email;
    document.getElementById('editUserRole').value  = user.role || 'user';
    document.getElementById('editUserForm').action = '<?php echo e(url("admin/users")); ?>/' + user.id;
    openModal('editUserModal');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/admin/users.blade.php ENDPATH**/ ?>