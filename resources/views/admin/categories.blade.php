@extends('admin.layout')
@section('title', 'Categories')

@section('content')
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="layout-grid" style="width:1.2rem;height:1.2rem;color:#10b981;stroke-width:2;"></i>
        </div>
        <div><h1 style="margin:0 0 .15rem;">Categories</h1><p style="margin:0;">Manage menu categories.</p></div>
    </div>
    <button onclick="openModal('addCatModal')" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
        <i data-lucide="plus" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i> Add Category
    </button>
</div>

{{-- Category cards --}}
@php
$lucideIcons = ['beef','flame','coffee','package','tag','utensils','pizza','soup','salad','sandwich'];
@endphp
<style>
#catCardsGrid{display:grid;grid-template-columns:repeat(2,1fr);gap:1.25rem;margin-bottom:2rem;}
@media(min-width:640px){#catCardsGrid{grid-template-columns:repeat(2,1fr);}}
@media(min-width:1024px){#catCardsGrid{grid-template-columns:repeat(4,1fr);}}
</style>
<div id="catCardsGrid">
    @foreach($categories as $cat)
    <div class="stat-card" style="border-color:{{ $cat->color }}22;position:relative;overflow:hidden;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.875rem;">
            <div style="width:2.75rem;height:2.75rem;border-radius:.875rem;background:{{ $cat->color }}18;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="{{ $cat->icon }}" style="width:1.25rem;height:1.25rem;color:{{ $cat->color }};stroke-width:2;"></i>
            </div>
            <span style="font-size:2rem;font-weight:800;color:{{ $cat->color }};line-height:1;">{{ $cat->active_menu_items_count }}</span>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-strong);margin:0 0 .25rem;">{{ $cat->name }}</h3>
        <p style="font-size:.75rem;color:var(--text-muted);margin:0 0 1rem;">{{ $cat->description ?: 'No description' }}</p>
        @if($cat->is_archived)
            <span class="badge badge-cancelled" style="display:inline-flex;align-items:center;gap:.25rem;margin-bottom:.75rem;">
                <i data-lucide="archive" style="width:.65rem;height:.65rem;stroke-width:2;"></i> Archived
            </span>
        @endif
        <a href="{{ route('admin.menu-items',['category'=>$cat->slug]) }}"
           style="font-size:.72rem;font-weight:600;color:{{ $cat->color }};text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;">
            Browse items <i data-lucide="arrow-right" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i>
        </a>
        <div style="position:absolute;bottom:-1.5rem;right:-1.5rem;width:5rem;height:5rem;border-radius:50%;background:{{ $cat->color }}12;filter:blur(16px);pointer-events:none;"></div>
    </div>
    @endforeach
</div>

{{-- Per-category item tables --}}
@foreach($categories as $cat)
@php $items = \App\Models\MenuItem::with('category')->active()->byCategory($cat->slug)->orderBy('sort_order')->get(); @endphp
<div class="section-card mb-5">
    <div style="padding:.875rem 1.25rem;border-bottom:1px solid var(--border-divider);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.625rem;">
            <div style="width:1.875rem;height:1.875rem;border-radius:.5rem;background:{{ $cat->color }}18;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="{{ $cat->icon }}" style="width:.85rem;height:.85rem;color:{{ $cat->color }};stroke-width:2.5;"></i>
            </div>
            <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">{{ $cat->name }}</h2>
            <span class="badge" style="background:var(--border-card);color:var(--text-muted);">{{ $items->count() }} items</span>
        </div>
        <a href="{{ route('admin.menu-items',['category'=>$cat->slug]) }}"
           style="font-size:.75rem;color:{{ $cat->color }};text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:.25rem;">
            View all <i data-lucide="arrow-right" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i>
        </a>
    </div>
    <table class="admin-table">
        <thead><tr><th>Item</th><th>Description</th><th>Price</th><th>Featured</th><th style="text-align:center;">Actions</th></tr></thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" style="width:2.5rem;height:2.5rem;border-radius:.5rem;object-fit:cover;border:1px solid var(--border-card);">
                        <span style="font-weight:500;color:var(--text-strong);font-size:.875rem;">{{ $item->name }}</span>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:.75rem;max-width:240px;">{{ Str::limit($item->description, 60) }}</td>
                <td style="font-weight:700;color:{{ $cat->color }};">₱{{ number_format($item->price) }}</td>
                <td>
                    @if($item->featured)
                        <span class="badge badge-delivered" style="display:inline-flex;align-items:center;gap:.25rem;">
                            <i data-lucide="star" style="width:.65rem;height:.65rem;fill:currentColor;stroke-width:0;"></i> Yes
                        </span>
                    @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:center;gap:.375rem;">
                        <button onclick='openEditMenuItem(@json($item), {{ $categories->toJson() }})' class="btn-icon-edit" title="Edit">
                            <i data-lucide="pencil" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.menu-items.archive', $item) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $item->is_archived ? 'btn-icon-restore' : 'btn-icon-archive' }}" title="{{ $item->is_archived ? 'Restore' : 'Archive' }}"
                                    onclick="return confirm('{{ $item->is_archived ? 'Restore' : 'Archive' }} {{ addslashes($item->name) }}?')">
                                <i data-lucide="{{ $item->is_archived ? 'archive-restore' : 'archive' }}" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.menu-items.delete', $item) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon-delete" title="Delete"
                                    onclick="return confirm('Delete {{ addslashes($item->name) }}? Cannot be undone.')">
                                <i data-lucide="trash-2" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">No items in this category.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

{{-- ══════════ ADD CATEGORY MODAL ══════════ --}}
<div id="addCatModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'addCatModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="layout-grid" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;"></i>
                <h3 class="modal-title">Add Category</h3>
            </div>
            <button onclick="closeModal('addCatModal')" class="modal-close"><i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" class="admin-input" placeholder="e.g. Desserts" required>
                    </div>
                    <div class="form-group">
                        <label>Lucide Icon <span style="color:#dc2626;">*</span></label>
                        <select name="icon" class="admin-input" required>
                            @foreach(['beef','flame','coffee','package','tag','utensils','pizza','soup','salad','sandwich','cup-soda','ice-cream'] as $ico)
                                <option value="{{ $ico }}">{{ $ico }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Accent Color <span style="color:#dc2626;">*</span></label>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <input type="color" name="color" value="#6366f1" style="width:2.5rem;height:2.5rem;border:1px solid var(--border-input);border-radius:.5rem;cursor:pointer;padding:.1rem;background:var(--bg-input);">
                            <span style="font-size:.75rem;color:var(--text-muted);">Pick a color</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="admin-input" placeholder="Short description…">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addCatModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                    <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ EDIT CATEGORY MODAL ══════════ --}}
<div id="editCatModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'editCatModal')">
    <div class="modal-box">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="pencil" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;"></i>
                <h3 class="modal-title">Edit Category</h3>
            </div>
            <button onclick="closeModal('editCatModal')" class="modal-close"><i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i></button>
        </div>
        <form method="POST" id="editCatForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" id="editCatName" class="admin-input" required>
                    </div>
                    <div class="form-group">
                        <label>Lucide Icon <span style="color:#dc2626;">*</span></label>
                        <select name="icon" id="editCatIcon" class="admin-input" required>
                            @foreach(['beef','flame','coffee','package','tag','utensils','pizza','soup','salad','sandwich','cup-soda','ice-cream'] as $ico)
                                <option value="{{ $ico }}">{{ $ico }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Accent Color <span style="color:#dc2626;">*</span></label>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <input type="color" name="color" id="editCatColor" style="width:2.5rem;height:2.5rem;border:1px solid var(--border-input);border-radius:.5rem;cursor:pointer;padding:.1rem;background:var(--bg-input);">
                            <span style="font-size:.75rem;color:var(--text-muted);">Pick a color</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" id="editCatDesc" class="admin-input" placeholder="Short description…">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editCatModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                    <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ EDIT MENU ITEM MODAL (from category tables) ══════════ --}}
<div id="editMenuItemModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'editMenuItemModal')">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="utensils" style="width:1.1rem;height:1.1rem;color:#f59e0b;stroke-width:2;"></i>
                <h3 class="modal-title">Edit Menu Item</h3>
            </div>
            <button onclick="closeModal('editMenuItemModal')" class="modal-close"><i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i></button>
        </div>
        <form method="POST" id="editMenuItemForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Item Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="name" id="editItemName" class="admin-input" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category <span style="color:#dc2626;">*</span></label>
                        <select name="category_id" id="editItemCategory" class="admin-input" required></select>
                    </div>
                    <div class="form-group">
                        <label>Price (₱) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="price" id="editItemPrice" class="admin-input" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="editItemDesc" class="admin-input" rows="2" style="resize:vertical;"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Image Path</label>
                        <input type="text" name="image" id="editItemImage" class="admin-input" placeholder="/images/hero-burger.webp">
                    </div>
                    <div class="form-group" style="justify-content:flex-end;padding-bottom:.25rem;">
                        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;user-select:none;margin-top:auto;">
                            <input type="checkbox" name="featured" id="editItemFeatured" value="1" style="width:1rem;height:1rem;accent-color:var(--accent);">
                            <span style="font-size:.8rem;color:var(--text-body);text-transform:none;letter-spacing:0;font-weight:500;">Mark as featured</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editMenuItemModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                    <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCat(cat) {
    document.getElementById('editCatName').value  = cat.name;
    document.getElementById('editCatIcon').value  = cat.icon;
    document.getElementById('editCatColor').value = cat.color;
    document.getElementById('editCatDesc').value  = cat.description || '';
    document.getElementById('editCatForm').action = '{{ url("admin/categories") }}/' + cat.id;
    openModal('editCatModal');
}
function openEditMenuItem(item, categories) {
    document.getElementById('editItemName').value     = item.name;
    document.getElementById('editItemPrice').value    = item.price;
    document.getElementById('editItemDesc').value     = item.description || '';
    document.getElementById('editItemImage').value    = item.image || '';
    document.getElementById('editItemFeatured').checked = !!item.featured;
    var sel = document.getElementById('editItemCategory');
    sel.innerHTML = '';
    categories.forEach(function(c){
        var o = document.createElement('option');
        o.value = c.id; o.textContent = c.name;
        if(c.id == item.category_id) o.selected = true;
        sel.appendChild(o);
    });
    document.getElementById('editMenuItemForm').action = '{{ url("admin/menu-items") }}/' + item.id;
    openModal('editMenuItemModal');
}
</script>
@endsection
