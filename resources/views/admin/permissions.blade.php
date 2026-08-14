@extends('admin.layout')
@section('title', 'Permissions & Access Control')

@section('content')
<div class="page-header">
    <h1>Permissions & Access Control</h1>
    <p>Manage role-based access control (RBAC) for your application</p>
</div>

{{-- Tabs --}}
<div class="permission-tabs">
    <button class="tab-btn active" data-tab="role-permissions">Role Permissions</button>
    <button class="tab-btn" data-tab="all-permissions">All Permissions</button>
    <button class="tab-btn" data-tab="user-permissions">User Overrides</button>
</div>

{{-- Tab 1: Role Permissions --}}
<div id="role-permissions" class="tab-content active">
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2 style="font-size:1rem;font-weight:600;margin:0 0 .25rem;">Role-Based Permissions</h2>
                <p style="font-size:.8rem;color:var(--text-muted);margin:0;">Configure which permissions each role has by default</p>
            </div>
        </div>

        <div class="p-5">
            {{-- Role Selector --}}
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.8rem;font-weight:600;color:var(--text-label);margin-bottom:.5rem;">Select Role</label>
                <select id="roleSelector" style="width:100%;max-width:300px;padding:.625rem .875rem;background:var(--bg-input);border:1px solid var(--border-input);border-radius:.5rem;color:var(--text-input);font-size:.875rem;">
                    @foreach($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Permissions by Group --}}
            <div id="rolePermissionsContainer">
                @foreach($permissionsByGroup as $group => $perms)
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-size:.875rem;font-weight:700;color:var(--text-strong);margin:0 0 .75rem;text-transform:uppercase;letter-spacing:.05em;">{{ $group }}</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.75rem;">
                        @foreach($perms as $perm)
                        <label class="permission-checkbox-card">
                            <input type="checkbox" 
                                   class="role-permission-check" 
                                   data-permission-id="{{ $perm->id }}"
                                   {{ in_array($perm->id, $rolePermissions[$roles[0]] ?? []) ? 'checked' : '' }}>
                            <div>
                                <div style="font-weight:600;font-size:.875rem;color:var(--text-strong);">{{ $perm->name }}</div>
                                @if($perm->description)
                                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem;">{{ $perm->description }}</div>
                                @endif
                                <div style="font-size:.7rem;color:var(--text-muted);margin-top:.3rem;font-family:monospace;">{{ $perm->slug }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border-divider);">
                <button id="saveRolePermissions" class="btn-primary">
                    <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                    Save Role Permissions
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tab 2: All Permissions --}}
<div id="all-permissions" class="tab-content">
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2 style="font-size:1rem;font-weight:600;margin:0 0 .25rem;">All Permissions</h2>
                <p style="font-size:.8rem;color:var(--text-muted);margin:0;">Manage system permissions</p>
            </div>
            <button id="btnNewPermission" class="btn-primary">
                <i data-lucide="plus" style="width:1rem;height:1rem;"></i>
                New Permission
            </button>
        </div>

        <div class="p-5">
            @foreach($permissionsByGroup as $group => $perms)
            <div style="margin-bottom:2rem;">
                <h3 style="font-size:.875rem;font-weight:700;color:var(--text-strong);margin:0 0 .75rem;text-transform:uppercase;letter-spacing:.05em;">{{ $group }}</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perms as $perm)
                        <tr data-permission-id="{{ $perm->id }}">
                            <td style="font-weight:600;">{{ $perm->name }}</td>
                            <td><code style="font-size:.75rem;background:var(--bg-input);padding:.2rem .4rem;border-radius:.3rem;">{{ $perm->slug }}</code></td>
                            <td style="font-size:.8rem;color:var(--text-muted);">{{ $perm->description ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $perm->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $perm->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:.5rem;">
                                    <button class="btn-icon btn-edit-permission" data-permission='@json($perm)' title="Edit">
                                        <i data-lucide="pencil" style="width:.875rem;height:.875rem;"></i>
                                    </button>
                                    <button class="btn-icon btn-toggle-permission" data-id="{{ $perm->id }}" data-active="{{ $perm->is_active }}" title="Toggle Status">
                                        <i data-lucide="{{ $perm->is_active ? 'eye-off' : 'eye' }}" style="width:.875rem;height:.875rem;"></i>
                                    </button>
                                    <button class="btn-icon btn-delete-permission" data-id="{{ $perm->id }}" title="Delete">
                                        <i data-lucide="trash-2" style="width:.875rem;height:.875rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tab 3: User Overrides --}}
<div id="user-permissions" class="tab-content">
    <div class="section-card">
        <div class="px-5 py-4 card-header-border">
            <h2 style="font-size:1rem;font-weight:600;margin:0 0 .25rem;">User-Specific Permission Overrides</h2>
            <p style="font-size:.8rem;color:var(--text-muted);margin:0;">Grant or revoke specific permissions for individual users</p>
        </div>

        <div class="p-5">
            <p style="text-align:center;color:var(--text-muted);padding:2rem;">
                Go to <a href="{{ route('admin.users') }}" style="color:var(--accent);text-decoration:none;">Users</a> to manage user-specific permissions
            </p>
        </div>
    </div>
</div>

{{-- Modal: New/Edit Permission --}}
<div id="permissionModal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 id="modalTitle">New Permission</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="permissionForm">
                <input type="hidden" id="permissionId">
                
                <div class="form-group">
                    <label for="permissionName">Permission Name *</label>
                    <input type="text" id="permissionName" class="form-input" placeholder="e.g., Manage Orders" required>
                </div>

                <div class="form-group">
                    <label for="permissionSlug">Slug *</label>
                    <input type="text" id="permissionSlug" class="form-input" placeholder="e.g., manage_orders" required>
                    <small style="color:var(--text-muted);font-size:.75rem;">Auto-generated from name. Use underscores, lowercase.</small>
                </div>

                <div class="form-group">
                    <label for="permissionGroup">Group *</label>
                    <input type="text" id="permissionGroup" class="form-input" placeholder="e.g., orders" required list="groupsList">
                    <datalist id="groupsList">
                        @foreach($permissionsByGroup->keys() as $group)
                        <option value="{{ $group }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="permissionDescription">Description</label>
                    <textarea id="permissionDescription" class="form-input" rows="3" placeholder="Brief description of what this permission allows..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary modal-close">Cancel</button>
            <button id="btnSavePermission" class="btn-primary">Save Permission</button>
        </div>
    </div>
</div>

@push('head')
<style>
.permission-tabs {
    display: flex;
    gap: .5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-divider);
}
.tab-btn {
    padding: .75rem 1.25rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text-muted);
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}
.tab-btn:hover {
    color: var(--text-strong);
    background: var(--accent-soft);
}
.tab-btn.active {
    color: var(--accent);
    border-bottom-color: var(--accent);
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
.permission-checkbox-card {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .875rem;
    background: var(--bg-input);
    border: 1px solid var(--border-input);
    border-radius: .5rem;
    cursor: pointer;
    transition: all .2s;
}
.permission-checkbox-card:hover {
    border-color: var(--accent);
    background: var(--accent-soft);
}
.permission-checkbox-card input[type="checkbox"] {
    margin-top: .15rem;
    width: 1rem;
    height: 1rem;
    cursor: pointer;
}
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal.show {
    display: flex;
}
.modal-content {
    background: var(--bg-card);
    border-radius: .75rem;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-divider);
}
.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-strong);
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .375rem;
    transition: all .2s;
}
.modal-close:hover {
    background: var(--bg-hover-row);
    color: var(--text-strong);
}
.modal-body {
    padding: 1.5rem;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border-divider);
}
.form-group {
    margin-bottom: 1.25rem;
}
.form-group label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    color: var(--text-label);
    margin-bottom: .5rem;
}
.form-input {
    width: 100%;
    padding: .625rem .875rem;
    background: var(--bg-input);
    border: 1px solid var(--border-input);
    border-radius: .5rem;
    color: var(--text-input);
    font-size: .875rem;
    transition: border-color .2s;
}
.form-input:focus {
    outline: none;
    border-color: var(--accent);
}
.btn-icon {
    padding: .4rem;
    background: transparent;
    border: 1px solid var(--border-ghost);
    border-radius: .375rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn-icon:hover {
    background: var(--accent-soft);
    border-color: var(--accent);
    color: var(--accent);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rolePermissionsData = @json($rolePermissions);
    const permissions = @json($permissions->keyBy('id'));
    
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
            lucide.createIcons();
        });
    });

    // Role selector change
    const roleSelector = document.getElementById('roleSelector');
    roleSelector.addEventListener('change', function() {
        const role = this.value;
        const rolePerms = rolePermissionsData[role] || [];
        
        document.querySelectorAll('.role-permission-check').forEach(checkbox => {
            checkbox.checked = rolePerms.includes(parseInt(checkbox.dataset.permissionId));
        });
    });

    // Save role permissions
    document.getElementById('saveRolePermissions').addEventListener('click', async function() {
        const role = roleSelector.value;
        const selectedPermissions = Array.from(document.querySelectorAll('.role-permission-check:checked'))
            .map(cb => parseInt(cb.dataset.permissionId));

        try {
            const response = await fetch('{{ route("admin.permissions.role-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ role, permissions: selectedPermissions })
            });

            const data = await response.json();
            if (data.success) {
                alert('Role permissions updated successfully!');
                rolePermissionsData[role] = selectedPermissions;
            } else {
                alert('Error: ' + (data.message || 'Failed to update permissions'));
            }
        } catch (error) {
            console.error(error);
            alert('Network error. Please try again.');
        }
    });

    // New permission button
    document.getElementById('btnNewPermission').addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'New Permission';
        document.getElementById('permissionForm').reset();
        document.getElementById('permissionId').value = '';
        document.getElementById('permissionModal').classList.add('show');
    });

    // Edit permission
    document.querySelectorAll('.btn-edit-permission').forEach(btn => {
        btn.addEventListener('click', function() {
            const perm = JSON.parse(this.dataset.permission);
            document.getElementById('modalTitle').textContent = 'Edit Permission';
            document.getElementById('permissionId').value = perm.id;
            document.getElementById('permissionName').value = perm.name;
            document.getElementById('permissionSlug').value = perm.slug;
            document.getElementById('permissionGroup').value = perm.group;
            document.getElementById('permissionDescription').value = perm.description || '';
            document.getElementById('permissionModal').classList.add('show');
        });
    });

    // Auto-generate slug from name
    document.getElementById('permissionName').addEventListener('input', async function() {
        if (!document.getElementById('permissionId').value) {
            const response = await fetch('{{ route("admin.permissions.generate-slug") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name: this.value })
            });
            const data = await response.json();
            document.getElementById('permissionSlug').value = data.slug;
        }
    });

    // Save permission
    document.getElementById('btnSavePermission').addEventListener('click', async function() {
        const id = document.getElementById('permissionId').value;
        const formData = {
            name: document.getElementById('permissionName').value,
            slug: document.getElementById('permissionSlug').value,
            group: document.getElementById('permissionGroup').value,
            description: document.getElementById('permissionDescription').value
        };

        try {
            const url = id 
                ? '{{ route("admin.permissions.update", ":id") }}'.replace(':id', id)
                : '{{ route("admin.permissions.store") }}';
            
            const response = await fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save permission'));
            }
        } catch (error) {
            console.error(error);
            alert('Network error. Please try again.');
        }
    });

    // Toggle permission active status
    document.querySelectorAll('.btn-toggle-permission').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            try {
                const response = await fetch('{{ route("admin.permissions.toggle-active", ":id") }}'.replace(':id', id), {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error(error);
                alert('Error toggling permission status');
            }
        });
    });

    // Delete permission
    document.querySelectorAll('.btn-delete-permission').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to delete this permission?')) return;

            const id = this.dataset.id;
            try {
                const response = await fetch('{{ route("admin.permissions.destroy", ":id") }}'.replace(':id', id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    location.reload();
                }
            } catch (error) {
                console.error(error);
                alert('Error deleting permission');
            }
        });
    });

    // Close modal
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
        });
    });

    // Close modal on backdrop click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });

    lucide.createIcons();
});
</script>
@endpush
@endsection
