@extends('admin.layout')
@section('title', 'Menu Items')

@section('content')
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(245,158,11,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="utensils" style="width:1.2rem;height:1.2rem;color:#f59e0b;stroke-width:2;"></i>
        </div>
        <div><h1 style="margin:0 0 .15rem;">Menu Items</h1><p style="margin:0;">{{ $items->count() }} items shown.</p></div>
    </div>
    <div style="display:flex;gap:.625rem;">
        <a href="{{ route('admin.menu-items', array_merge(request()->query(), ['archived' => request('archived') ? null : '1'])) }}"
           class="btn-ghost" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;">
            <i data-lucide="{{ request('archived') ? 'eye' : 'archive' }}" style="width:.85rem;height:.85rem;stroke-width:2;"></i>
            {{ request('archived') ? 'View Active' : 'View Archived' }}
        </a>
        <button onclick="openAddItemModal()" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
            <i data-lucide="plus" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i> Add Item
        </button>
    </div>
</div>

{{-- Stat Cards --}}
@php
$allCats = \App\Models\Category::active()->withCount(['activeMenuItems'])->orderBy('sort_order')->get();
$summaryCards = $allCats->map(fn($c) => ['slug'=>$c->slug,'icon'=>$c->icon,'label'=>$c->name,'color'=>$c->color,'bg'=>$c->color.'18','count'=>$c->active_menu_items_count]);
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    @foreach($summaryCards as $sc)
    <a href="{{ route('admin.menu-items',['category'=>$sc['slug']]) }}" class="stat-card"
       style="text-decoration:none;position:relative;overflow:hidden;border-color:{{ $sc['color'] }}22;
              {{ request('category')===$sc['slug'] ? 'border-color:'.$sc['color'].'55;box-shadow:0 0 0 3px '.$sc['color'].'18;' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.875rem;">
            <div style="width:3rem;height:3rem;border-radius:.875rem;background:{{ $sc['bg'] }};display:flex;align-items:center;justify-content:center;">
                <i data-lucide="{{ $sc['icon'] }}" style="width:1.4rem;height:1.4rem;color:{{ $sc['color'] }};stroke-width:2;"></i>
            </div>
            <span style="font-size:2.5rem;font-weight:900;color:{{ $sc['color'] }};line-height:1;">{{ $sc['count'] }}</span>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-strong);margin:0 0 .25rem;">{{ $sc['label'] }}</h3>
        <span style="font-size:.72rem;font-weight:600;color:{{ $sc['color'] }};display:inline-flex;align-items:center;gap:.3rem;">
            Browse <i data-lucide="arrow-right" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i>
        </span>
        <div style="position:absolute;bottom:-1.5rem;right:-1.5rem;width:5.5rem;height:5.5rem;border-radius:50%;background:{{ $sc['bg'] }};filter:blur(18px);pointer-events:none;"></div>
    </a>
    @endforeach
</div>

{{-- Table Card --}}
<div class="section-card">
    <form method="GET" action="{{ route('admin.menu-items') }}" class="filter-bar">
        <div style="position:relative;max-width:240px;width:100%;">
            <i data-lucide="search" style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);width:.9rem;height:.9rem;color:var(--text-muted);stroke-width:2;pointer-events:none;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items…" class="admin-input" style="padding-left:2.1rem;">
        </div>
        <select name="category" class="admin-input" style="max-width:160px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category')===$cat->slug ? 'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.35rem;">
            <i data-lucide="filter" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Filter
        </button>
        @if(request('search') || request('category'))
            <a href="{{ route('admin.menu-items') }}" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.35rem;">
                <i data-lucide="x" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Clear
            </a>
        @endif
    </form>
    <div style="padding:.75rem 1.25rem;border-bottom:1px solid var(--border-divider);display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('admin.menu-items') }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .875rem;border-radius:9999px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .2s;{{ !request('category') ? 'background:var(--accent);color:#fff;' : 'background:var(--border-card);color:var(--text-muted);' }}">
            <i data-lucide="layers" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i> All
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('admin.menu-items',['category'=>$cat->slug]) }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .875rem;border-radius:9999px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .2s;{{ request('category')===$cat->slug ? 'background:'.$cat->color.';color:#fff;' : 'background:var(--border-card);color:var(--text-muted);' }}">
            <i data-lucide="{{ $cat->icon }}" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i> {{ $cat->name }}
        </a>
        @endforeach
    </div>
    <table class="admin-table">
        <thead>
            <tr><th>Item</th><th>Category</th><th>Base Price</th><th>Flavors</th><th>Modifiers</th><th>Add-ons</th><th>Featured</th><th>Status</th><th style="text-align:center;">Actions</th></tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr style="{{ $item->is_archived ? 'opacity:.55;' : '' }}">
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" style="width:3rem;height:3rem;border-radius:.75rem;object-fit:cover;border:1px solid var(--border-card);flex-shrink:0;">
                        <div>
                            <span style="font-weight:600;color:var(--text-strong);display:block;">{{ $item->name }}</span>
                            <span style="font-size:.72rem;color:var(--text-muted);">{{ Str::limit($item->description,45) }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge" style="background:{{ $item->category->color }}18;color:{{ $item->category->color }};display:inline-flex;align-items:center;gap:.3rem;">
                        <i data-lucide="{{ $item->category->icon }}" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i>
                        {{ $item->category->name }}
                    </span>
                </td>
                <td style="font-weight:700;color:{{ $item->category->color }};">₱{{ number_format($item->price,2) }}</td>
                <td>
                    @php $flavors = $item->modifierGroups->where('type','flavor'); @endphp
                    @if($flavors->count())
                        <span class="badge badge-preparing" style="display:inline-flex;align-items:center;gap:.25rem;">
                            <i data-lucide="droplets" style="width:.65rem;height:.65rem;stroke-width:2;"></i> {{ $flavors->count() }} group{{ $flavors->count()>1?'s':'' }}
                        </span>
                    @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    @endif
                </td>
                <td>
                    @php $mods = $item->modifierGroups->where('type','modifier'); @endphp
                    @if($mods->count())
                        <span class="badge badge-out" style="display:inline-flex;align-items:center;gap:.25rem;">
                            <i data-lucide="sliders-horizontal" style="width:.65rem;height:.65rem;stroke-width:2;"></i> {{ $mods->count() }} group{{ $mods->count()>1?'s':'' }}
                        </span>
                    @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    @endif
                </td>
                <td>
                    @php $addons = $item->modifierGroups->where('type','addon'); @endphp
                    @if($addons->count())
                        <span class="badge" style="background:rgba(245,158,11,.1);color:#d97706;display:inline-flex;align-items:center;gap:.25rem;">
                            <i data-lucide="package-plus" style="width:.65rem;height:.65rem;stroke-width:2;"></i> {{ $addons->count() }} add-on{{ $addons->count()>1?'s':'' }}
                        </span>
                    @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    @endif
                </td>
                <td>
                    @if($item->featured)
                        <span class="badge badge-delivered" style="display:inline-flex;align-items:center;gap:.25rem;"><i data-lucide="star" style="width:.65rem;height:.65rem;fill:currentColor;stroke-width:0;"></i> Yes</span>
                    @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                    @endif
                </td>
                <td>
                    @if($item->is_archived)
                        <span class="badge badge-cancelled" style="display:inline-flex;align-items:center;gap:.25rem;"><i data-lucide="archive" style="width:.65rem;height:.65rem;stroke-width:2;"></i> Archived</span>
                    @else
                        <span class="badge badge-delivered" style="display:inline-flex;align-items:center;gap:.25rem;"><i data-lucide="check-circle" style="width:.65rem;height:.65rem;stroke-width:2;"></i> Active</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;align-items:center;justify-content:center;gap:.375rem;">
                        <button onclick='openEditItemModal(@json($item->load("modifierGroups.options")), {{ $categories->toJson() }})' class="btn-icon-edit" title="Edit">
                            <i data-lucide="pencil" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.menu-items.archive',$item) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $item->is_archived ? 'btn-icon-restore' : 'btn-icon-archive' }}" title="{{ $item->is_archived?'Restore':'Archive' }}"
                                    onclick="return confirm('{{ $item->is_archived?'Restore':'Archive' }} {{ addslashes($item->name) }}?')">
                                <i data-lucide="{{ $item->is_archived?'archive-restore':'archive' }}" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.menu-items.delete',$item) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon-delete" title="Delete"
                                    onclick="return confirm('Permanently delete {{ addslashes($item->name) }}?')">
                                <i data-lucide="trash-2" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:3rem;">No items found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══════════════════════════════════════════════════════
     ADD / EDIT ITEM MODAL  (shared, toggled by JS)
══════════════════════════════════════════════════════ --}}
<div id="itemModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'itemModal')">
  <div class="modal-box" style="max-width:720px;width:100%;">

    {{-- Header --}}
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:.5rem;">
        <i data-lucide="utensils" style="width:1.1rem;height:1.1rem;color:#f59e0b;stroke-width:2;"></i>
        <h3 class="modal-title" id="itemModalTitle">Add Menu Item</h3>
      </div>
      <button onclick="closeModal('itemModal')" class="modal-close">
        <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
      </button>
    </div>

    {{-- Tab bar --}}
    <div style="display:flex;border-bottom:1px solid var(--border-divider);padding:0 1.4rem;gap:.25rem;">
      <button type="button" onclick="switchTab('tabBasic')" id="btnTabBasic"
              style="padding:.7rem 1rem;font-size:.8rem;font-weight:600;border:none;background:none;cursor:pointer;color:var(--accent);border-bottom:2px solid var(--accent);transition:all .2s;">
        <i data-lucide="info" style="width:.8rem;height:.8rem;stroke-width:2;vertical-align:middle;"></i> Basic Info
      </button>
      <button type="button" onclick="switchTab('tabCustomize')" id="btnTabCustomize"
              style="padding:.7rem 1rem;font-size:.8rem;font-weight:600;border:none;background:none;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;transition:all .2s;">
        <i data-lucide="sliders-horizontal" style="width:.8rem;height:.8rem;stroke-width:2;vertical-align:middle;"></i> Customization
      </button>
      <button type="button" onclick="switchTab('tabAddons')" id="btnTabAddons"
              style="padding:.7rem 1rem;font-size:.8rem;font-weight:600;border:none;background:none;cursor:pointer;color:var(--text-muted);border-bottom:2px solid transparent;transition:all .2s;">
        <i data-lucide="package-plus" style="width:.8rem;height:.8rem;stroke-width:2;vertical-align:middle;"></i> Add-ons
      </button>
    </div>

    <form method="POST" id="itemModalForm">
      @csrf
      <input type="hidden" name="_method" id="itemModalMethod" value="POST">

      {{-- ── TAB 1: BASIC INFO ── --}}
      <div id="tabBasic" class="modal-body tab-pane" style="display:flex;">
        <div class="form-group" style="width:100%;">
          <label>Item Name <span style="color:#dc2626;">*</span></label>
          <input type="text" name="name" id="iName" class="admin-input" placeholder="e.g. Crispy Chicken Burger" required>
        </div>
        <div class="form-row" style="width:100%;">
          <div class="form-group">
            <label>Category <span style="color:#dc2626;">*</span></label>
            <select name="category_id" id="iCategory" class="admin-input" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Base Price (₱) <span style="color:#dc2626;">*</span></label>
            <input type="number" name="price" id="iPrice" class="admin-input" min="0" step="0.01" placeholder="350.00" required>
          </div>
        </div>
        <div class="form-group" style="width:100%;">
          <label>Description</label>
          <textarea name="description" id="iDesc" class="admin-input" rows="2" style="resize:vertical;" placeholder="Brief description…"></textarea>
        </div>
        <div class="form-row" style="width:100%;">
          <div class="form-group">
            <label>Image Path</label>
            <input type="text" name="image" id="iImage" class="admin-input" placeholder="/images/hero-burger.jpg">
          </div>
          <div class="form-group" style="justify-content:flex-end;">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;user-select:none;margin-top:auto;">
              <input type="checkbox" name="featured" id="iFeatured" value="1" style="width:1rem;height:1rem;accent-color:var(--accent);">
              <span style="font-size:.8rem;color:var(--text-body);text-transform:none;letter-spacing:0;font-weight:500;">Mark as featured</span>
            </label>
          </div>
        </div>
        <div class="form-group" style="width:100%;">
          <label style="display:flex;align-items:center;gap:.4rem;">
            <i data-lucide="activity" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Status
          </label>
          <select name="status" id="iStatus" class="admin-input">
            <option value="active">Active — visible to customers</option>
            <option value="archived">Archived — hidden from customers</option>
          </select>
        </div>
      </div>

      {{-- ── TAB 2: CUSTOMIZATION (Flavors + Modifiers combined) ── --}}
      <div id="tabCustomize" class="modal-body tab-pane" style="display:none;gap:0;padding:0;">

        {{-- FLAVORS section --}}
        <div style="padding:1.25rem 1.4rem 1rem;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <div>
              <p style="font-weight:700;color:var(--text-strong);margin:0 0 .2rem;font-size:.875rem;display:flex;align-items:center;gap:.4rem;">
                <i data-lucide="droplets" style="width:.85rem;height:.85rem;color:#3b82f6;stroke-width:2;"></i> Flavors
              </p>
              <p style="color:var(--text-muted);font-size:.72rem;margin:0;">e.g. Spice Level → Mild, Regular, Spicy. Optionally affects price.</p>
            </div>
            <button type="button" onclick="addGroup('flavor')" class="btn-primary" style="font-size:.75rem;padding:.4rem .85rem;display:inline-flex;align-items:center;gap:.35rem;white-space:nowrap;">
              <i data-lucide="plus" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Add Flavor Group
            </button>
          </div>
          <div id="flavorGroups" style="display:flex;flex-direction:column;gap:1rem;"></div>
          <p id="noFlavors" style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:1rem 0 .5rem;">
            No flavor groups yet. Click "Add Flavor Group" to start.
          </p>
        </div>

        {{-- Divider --}}
        <div style="height:1px;background:var(--border-divider);margin:0 1.4rem;"></div>

        {{-- MODIFIERS section --}}
        <div style="padding:1.25rem 1.4rem 1rem;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <div>
              <p style="font-weight:700;color:var(--text-strong);margin:0 0 .2rem;font-size:.875rem;display:flex;align-items:center;gap:.4rem;">
                <i data-lucide="sliders-horizontal" style="width:.85rem;height:.85rem;color:#8b5cf6;stroke-width:2;"></i> Modifiers
              </p>
              <p style="color:var(--text-muted);font-size:.72rem;margin:0;">e.g. Size → Small, Regular, Large. Optionally affects price.</p>
            </div>
            <button type="button" onclick="addGroup('modifier')" class="btn-primary" style="font-size:.75rem;padding:.4rem .85rem;display:inline-flex;align-items:center;gap:.35rem;white-space:nowrap;">
              <i data-lucide="plus" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Add Modifier Group
            </button>
          </div>
          <div id="modifierGroups" style="display:flex;flex-direction:column;gap:1rem;"></div>
          <p id="noModifiers" style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:1rem 0 .5rem;">
            No modifier groups yet. Click "Add Modifier Group" to start.
          </p>
        </div>

      </div>

      {{-- ── TAB 4: ADD-ONS ── --}}
      <div id="tabAddons" class="modal-body tab-pane" style="display:none;">
        {{-- Header row --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.875rem;gap:1rem;">
          <div>
            <p style="font-weight:600;color:var(--text-strong);margin:0 0 .2rem;font-size:.875rem;">Add-on Combos</p>
            <p style="color:var(--text-muted);font-size:.75rem;margin:0;">
              Pair this item with extras. e.g. "Wings with Milk" +₱30, "Wings with Rice" +₱50.<br>
              Each add-on can optionally add to the base price.
            </p>
          </div>
          <button type="button" onclick="addAddon()" class="btn-primary"
                  style="font-size:.75rem;padding:.4rem .85rem;display:inline-flex;align-items:center;gap:.35rem;white-space:nowrap;flex-shrink:0;">
            <i data-lucide="plus" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Add Add-on
          </button>
        </div>

        {{-- Column headers --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 110px 80px auto;gap:.5rem;padding:.3rem .5rem;margin-bottom:.25rem;">
          <span style="font-size:.68rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Add-on Name</span>
          <span style="font-size:.68rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Description (optional)</span>
          <span style="font-size:.68rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Extra Price</span>
          <span style="font-size:.68rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">Affects?</span>
          <span></span>
        </div>

        {{-- Addon rows container --}}
        <div id="addonRows" style="display:flex;flex-direction:column;gap:.4rem;"></div>

        <p id="noAddons" style="text-align:center;color:var(--text-muted);font-size:.8rem;padding:1.5rem 0;">
          No add-ons yet. Click "Add Add-on" to create combos like "Wings with Milk".
        </p>

        {{-- Price note --}}
        <div style="margin-top:.75rem;padding:.75rem;background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.15);border-radius:.5rem;display:flex;align-items:flex-start;gap:.5rem;">
          <i data-lucide="info" style="width:.875rem;height:.875rem;color:#6366f1;stroke-width:2;flex-shrink:0;margin-top:.1rem;"></i>
          <p style="font-size:.72rem;color:var(--text-muted);margin:0;line-height:1.5;">
            <strong style="color:var(--text-body);">Affects Price = Yes</strong> — customer pays base price + extra amount.<br>
            <strong style="color:var(--text-body);">Affects Price = No</strong> — add-on is included free (no extra charge).
          </p>
        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer">
        <button type="button" onclick="closeModal('itemModal')" class="btn-ghost">Cancel</button>
        <button type="button" onclick="switchTab('tabBasic')" id="btnPrevTab" class="btn-ghost" style="display:none;">
          <i data-lucide="arrow-left" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Back
        </button>
        <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
          <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i>
          <span id="itemModalBtnLabel">Create Item</span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     INLINE STYLES FOR MODIFIER BUILDER
══════════════════════════════════════════════════════ --}}
<style>
.group-card { background:var(--bg-filter);border:1px solid var(--border-card);border-radius:.75rem;padding:1rem;position:relative; }
.group-header { display:flex;align-items:center;gap:.625rem;margin-bottom:.875rem; }
.group-title { flex:1;font-size:.8rem;font-weight:700;color:var(--text-strong);background:transparent;border:none;border-bottom:1px dashed var(--border-input);padding:.2rem .1rem;width:100%;outline:none;color:var(--text-input); }
.group-title:focus { border-bottom-color:var(--accent); }
/* ── Option row: card-style ── */
.option-row { display:flex;align-items:center;gap:.5rem;padding:.6rem .75rem;background:var(--bg-body);border:1px solid var(--border-card);border-radius:.625rem;margin-bottom:.425rem;flex-wrap:wrap; }
.option-row:hover { border-color:var(--border-input); }
.opt-name  { flex:1;min-width:120px;font-size:.8rem; }
.opt-price-toggle { display:inline-flex;align-items:center;gap:.3rem;padding:.28rem .7rem;border-radius:9999px;font-size:.7rem;font-weight:700;cursor:pointer;border:1px solid;transition:all .2s;white-space:nowrap;user-select:none; }
.opt-price-toggle.free    { background:rgba(100,100,100,.08);color:var(--text-muted);border-color:var(--border-input); }
.opt-price-toggle.add     { background:rgba(16,185,129,.1); color:#16a34a;border-color:#16a34a; }
.opt-price-toggle.replace { background:rgba(99,102,241,.1);color:#6366f1;border-color:#6366f1; }
.opt-price-input { width:90px;font-size:.8rem;transition:all .2s; }
input[type=number].admin-input:disabled { opacity:.35; cursor:not-allowed; }
.pill-toggle { display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;border-radius:9999px;font-size:.7rem;font-weight:600;cursor:pointer;border:1px solid var(--border-input);background:transparent;color:var(--text-muted);transition:all .2s; }
.pill-toggle.on  { background:rgba(16,185,129,.12);color:#10b981;border-color:#10b981; }
.pill-toggle.off { background:rgba(239,68,68,.08);color:#ef4444;border-color:#ef4444; }
@media(max-width:540px){ .opt-price-toggle { font-size:.65rem; padding:.22rem .5rem; } }
</style>

<script>
// ─────────────────────────────────────────────────────────
// Tab switching
// ─────────────────────────────────────────────────────────
var TABS = ['tabBasic','tabCustomize','tabAddons'];
function switchTab(id) {
    TABS.forEach(function(t){
        var pane = document.getElementById(t);
        var key  = 'btn' + t.charAt(0).toUpperCase() + t.slice(1);
        var btn  = document.getElementById(key);
        if(pane){ pane.style.display = t===id ? 'flex':'none'; if(t===id) pane.style.flexDirection='column'; }
        if(btn) { btn.style.color = t===id ? 'var(--accent)':'var(--text-muted)'; btn.style.borderBottom = t===id ? '2px solid var(--accent)':'2px solid transparent'; }
    });
    document.getElementById('btnPrevTab').style.display = id==='tabBasic' ? 'none':'inline-flex';
    syncGroupVisibility();
    syncAddonVisibility();
    lucide.createIcons();
}

// ─────────────────────────────────────────────────────────
// Open Add modal
// ─────────────────────────────────────────────────────────
function openAddItemModal() {
    document.getElementById('itemModalTitle').textContent    = 'Add Menu Item';
    document.getElementById('itemModalBtnLabel').textContent = 'Create Item';
    document.getElementById('itemModalMethod').value         = 'POST';
    document.getElementById('itemModalForm').action          = '{{ route("admin.menu-items.store") }}';
    document.getElementById('iName').value      = '';
    document.getElementById('iPrice').value     = '';
    document.getElementById('iDesc').value      = '';
    document.getElementById('iImage').value     = '';
    document.getElementById('iFeatured').checked = false;
    document.getElementById('iStatus').value    = 'active';
    // Reset first category option
    var sel = document.getElementById('iCategory');
    if(sel.options.length) sel.selectedIndex = 0;
    clearAllGroups();
    switchTab('tabBasic');
    openModal('itemModal');
}

// ─────────────────────────────────────────────────────────
// Open Edit modal
// ─────────────────────────────────────────────────────────
function openEditItemModal(item, categories) {
    document.getElementById('itemModalTitle').textContent    = 'Edit: ' + item.name;
    document.getElementById('itemModalBtnLabel').textContent = 'Save Changes';
    document.getElementById('itemModalMethod').value         = 'PUT';
    document.getElementById('itemModalForm').action          = '{{ url("admin/menu-items") }}/' + item.id;
    document.getElementById('iName').value       = item.name;
    document.getElementById('iPrice').value      = item.price;
    document.getElementById('iDesc').value       = item.description || '';
    document.getElementById('iImage').value      = item.image || '';
    document.getElementById('iFeatured').checked = !!item.featured;
    document.getElementById('iStatus').value     = item.is_archived ? 'archived':'active';
    // Populate categories
    var sel = document.getElementById('iCategory');
    sel.innerHTML = '';
    categories.forEach(function(c){
        var o = document.createElement('option');
        o.value = c.id; o.textContent = c.name;
        if(c.id == item.category_id) o.selected = true;
        sel.appendChild(o);
    });
    // Load existing groups
    clearAllGroups();
    (item.modifier_groups || []).forEach(function(g){
        if(g.type === 'addon') {
            // Load as addon row — treat options[0] as the addon itself
            addAddon({
                id:               g.id,
                name:             g.name,
                description:      g.description || '',
                price_type:       (g.options && g.options[0]) ? g.options[0].price_type       : 'none',
                price_adjustment: (g.options && g.options[0]) ? g.options[0].price_adjustment : 0,
            });
        } else {
            loadGroup(g);
        }
    });
    switchTab('tabBasic');
    openModal('itemModal');
}
</script>

<script>
// ─────────────────────────────────────────────────────────
// Group builder
// ─────────────────────────────────────────────────────────
var groupCounter = 0;

function syncAddonVisibility() {
    var el = document.getElementById('addonRows');
    var noEl = document.getElementById('noAddons');
    if(el && noEl) noEl.style.display = el.children.length === 0 ? 'block' : 'none';
}

function syncGroupVisibility() {
    var fEl = document.getElementById('flavorGroups');
    var mEl = document.getElementById('modifierGroups');
    if(fEl) document.getElementById('noFlavors').style.display   = fEl.children.length === 0 ? 'block':'none';
    if(mEl) document.getElementById('noModifiers').style.display = mEl.children.length === 0 ? 'block':'none';
}

function clearAllGroups() {
    document.getElementById('flavorGroups').innerHTML   = '';
    document.getElementById('modifierGroups').innerHTML = '';
    document.getElementById('addonRows').innerHTML      = '';
    addonCounter = 0;
    groupCounter = 0;
    syncGroupVisibility();
    syncAddonVisibility();
}

function addGroup(type) {
function addGroup(type) {
    var defaultLabel = type === 'flavor' ? 'No Flavor' : 'No Modifier';
    loadGroup({
        type: type, name: '', required: false, is_active: true,
        options: [
            { name: defaultLabel, price_type: 'none', price_adjustment: 0, is_default: true, is_active: true }
        ]
    });
    switchTab('tabCustomize');
}

function loadGroup(g) {
    var type = g.type || 'modifier';
    var gid  = 'g_' + (++groupCounter);
    var container = document.getElementById(type === 'flavor' ? 'flavorGroups' : 'modifierGroups');

    var card = document.createElement('div');
    card.className = 'group-card';
    card.id = 'card_' + gid;
    card.dataset.type = type;
    card.dataset.existingId = g.id || '';

    var accentColor = type === 'flavor' ? '#3b82f6' : '#8b5cf6';
    var accentBg    = type === 'flavor' ? 'rgba(59,130,246,.08)' : 'rgba(139,92,246,.08)';
    var iconName    = type === 'flavor' ? 'droplets' : 'sliders-horizontal';

    card.innerHTML =
        '<div class="group-header">' +
            '<div style="width:1.75rem;height:1.75rem;border-radius:.4rem;background:'+accentBg+';display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                '<i data-lucide="'+iconName+'" style="width:.85rem;height:.85rem;color:'+accentColor+';stroke-width:2;"></i>' +
            '</div>' +
            '<input class="group-title" type="text" placeholder="Group name (e.g. Spice Level)" value="'+escHtml(g.name||'')+'" id="gtitle_'+gid+'">' +
            '<label style="display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:var(--text-muted);cursor:pointer;white-space:nowrap;">' +
                '<input type="checkbox" id="greq_'+gid+'" '+(g.required?'checked':'')+' style="accent-color:var(--accent);"> Required' +
            '</label>' +
            '<button type="button" onclick="toggleGroupActive(\''+gid+'\')" id="gact_'+gid+'" class="pill-toggle '+(g.is_active!==false?'on':'off')+'">' +
                '<i data-lucide="'+(g.is_active!==false?'check':'x')+'" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i>' +
                '<span>'+(g.is_active!==false?'Active':'Inactive')+'</span>' +
            '</button>' +
            '<button type="button" onclick="removeGroup(\''+gid+'\')" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:.2rem;" title="Remove group">' +
                '<i data-lucide="trash-2" style="width:.85rem;height:.85rem;stroke-width:2;"></i>' +
            '</button>' +
        '</div>' +
        '<div id="opts_'+gid+'" style="display:flex;flex-direction:column;gap:.375rem;"></div>' +
        '<button type="button" onclick="addOption(\''+gid+'\')" style="margin-top:.625rem;background:none;border:1px dashed var(--border-input);border-radius:.5rem;padding:.4rem .75rem;font-size:.72rem;color:var(--text-muted);cursor:pointer;width:100%;transition:all .2s;" '+
               'onmouseenter="this.style.background=\'var(--accent-soft)\';this.style.borderColor=\'var(--accent)\';this.style.color=\'var(--accent)\';" '+
               'onmouseleave="this.style.background=\'none\';this.style.borderColor=\'var(--border-input)\';this.style.color=\'var(--text-muted)\';">' +
            '+ Add Option' +
        '</button>';

    container.appendChild(card);

    (g.options || []).forEach(function(opt){ addOption(gid, opt); });
    syncGroupVisibility();
    lucide.createIcons();
}

function removeGroup(gid) {
    var card = document.getElementById('card_' + gid);
    if(card){ card.remove(); }
    syncGroupVisibility();
}

function toggleGroupActive(gid) {
    var btn  = document.getElementById('gact_' + gid);
    var isOn = btn.classList.contains('on');
    if(isOn) {
        btn.classList.replace('on','off');
        btn.querySelector('span').textContent = 'Inactive';
        btn.querySelector('i').setAttribute('data-lucide','x');
    } else {
        btn.classList.replace('off','on');
        btn.querySelector('span').textContent = 'Active';
        btn.querySelector('i').setAttribute('data-lucide','check');
    }
    lucide.createIcons();
}

</script>

<script>
// ─────────────────────────────────────────────────────────
// ADD-ONS builder
// ─────────────────────────────────────────────────────────
var addonCounter = 0;

function addAddon(addon) {
    addon = addon || {};
    var aid       = 'ad_' + (++addonCounter);
    var container = document.getElementById('addonRows');
    var affects   = (addon.price_type === 'add');
    var adjVal    = addon.price_adjustment || 0;

    var row = document.createElement('div');
    row.id  = 'addonrow_' + aid;
    row.dataset.existingId = addon.id || '';
    row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 110px 80px auto;gap:.5rem;align-items:center;' +
                        'padding:.55rem .75rem;background:var(--bg-body);border:1px solid var(--border-card);' +
                        'border-radius:.625rem;';

    row.innerHTML =
        // Name
        '<input type="text" class="admin-input" id="adname_'+aid+'" ' +
               'placeholder="e.g. Wings with Milk" ' +
               'value="'+escHtml(addon.name||'')+'" ' +
               'style="font-size:.8rem;">' +

        // Description
        '<input type="text" class="admin-input" id="addesc_'+aid+'" ' +
               'placeholder="e.g. 6pcs wings + 1 milk" ' +
               'value="'+escHtml(addon.description||'')+'" ' +
               'style="font-size:.8rem;">' +

        // Extra price
        '<div style="position:relative;">' +
            '<span style="position:absolute;left:.5rem;top:50%;transform:translateY(-50%);font-size:.75rem;color:var(--text-muted);pointer-events:none;">₱</span>' +
            '<input type="number" class="admin-input" id="adadj_'+aid+'" ' +
                   'min="0" step="0.01" value="'+adjVal+'" ' +
                   'placeholder="0.00" ' +
                   'style="padding-left:1.4rem;font-size:.8rem;'+(affects ? '' : 'opacity:.35;pointer-events:none;')+'">' +
        '</div>' +

        // Affects price toggle
        '<button type="button" id="adaffects_'+aid+'" ' +
                'onclick="toggleAddonAffects(\''+aid+'\')" ' +
                'style="font-size:.7rem;font-weight:700;padding:.28rem .6rem;border-radius:9999px;cursor:pointer;' +
                       'border:1px solid;transition:all .2s;white-space:nowrap;' +
                       (affects
                           ? 'background:rgba(22,163,74,.1);color:#16a34a;border-color:#16a34a;'
                           : 'background:rgba(100,100,100,.08);color:var(--text-muted);border-color:var(--border-input);') +
                '" data-affects="'+(affects?'1':'0')+'">' +
            (affects
                ? '<i data-lucide="check-circle" style="width:.7rem;height:.7rem;stroke-width:2;vertical-align:middle;"></i> Yes'
                : '<i data-lucide="minus-circle" style="width:.7rem;height:.7rem;stroke-width:2;vertical-align:middle;"></i> No') +
        '</button>' +

        // Remove
        '<button type="button" onclick="removeAddon(\''+aid+'\')" ' +
                'style="background:none;border:none;cursor:pointer;color:#ef4444;padding:.2rem;display:flex;align-items:center;" title="Remove">' +
            '<i data-lucide="x-circle" style="width:.9rem;height:.9rem;stroke-width:2;"></i>' +
        '</button>';

    container.appendChild(row);
    syncAddonVisibility();
    lucide.createIcons();
}

function toggleAddonAffects(aid) {
    var btn    = document.getElementById('adaffects_' + aid);
    var adj    = document.getElementById('adadj_'     + aid);
    var isYes  = btn.dataset.affects === '1';
    var nowYes = !isYes;

    btn.dataset.affects = nowYes ? '1' : '0';
    btn.innerHTML = nowYes
        ? '<i data-lucide="check-circle" style="width:.7rem;height:.7rem;stroke-width:2;vertical-align:middle;"></i> Yes'
        : '<i data-lucide="minus-circle" style="width:.7rem;height:.7rem;stroke-width:2;vertical-align:middle;"></i> No';
    btn.style.background   = nowYes ? 'rgba(22,163,74,.1)'         : 'rgba(100,100,100,.08)';
    btn.style.color        = nowYes ? '#16a34a'                     : 'var(--text-muted)';
    btn.style.borderColor  = nowYes ? '#16a34a'                     : 'var(--border-input)';
    adj.style.opacity      = nowYes ? '1'   : '.35';
    adj.style.pointerEvents= nowYes ? 'auto': 'none';
    if(!nowYes) adj.value  = 0;
    lucide.createIcons();
}

function removeAddon(aid) {
    var row = document.getElementById('addonrow_' + aid);
    if(row) row.remove();
    syncAddonVisibility();
}

</script>
<script>
// ─────────────────────────────────────────────────────────
// Option rows
// ─────────────────────────────────────────────────────────
var optCounter = 0;

function addOption(gid, opt) {
    opt = opt || {};
    var oid       = 'o_' + (++optCounter);
    var container = document.getElementById('opts_' + gid);
    var priceType = opt.price_type || 'none';

    // Label + style for the price-toggle button
    var ptLabels = { none:'Free (no price change)', add:'+Add to price', replace:'=Replace price' };
    var ptClass  = priceType === 'none' ? 'free' : (priceType === 'add' ? 'add' : 'replace');
    var ptIcon   = priceType === 'none' ? 'tag' : (priceType === 'add' ? 'plus-circle' : 'repeat');

    var row = document.createElement('div');
    row.className = 'option-row';
    row.id = 'optrow_' + oid;
    if(opt.id) row.dataset.existingId = opt.id;
    row.dataset.gid = gid;

    row.innerHTML =
        // Name
        '<input type="text" class="admin-input opt-name" id="oname_'+oid+'"' +
            ' placeholder="Option name (e.g. Large, Spicy, Extra Cheese)"' +
            ' value="'+escHtml(opt.name||'')+'">' +

        // "Affects price?" toggle pill — cycles: none → add → replace → none
        '<button type="button" class="opt-price-toggle '+ptClass+'" id="optoggle_'+oid+'"' +
                ' data-ptype="'+priceType+'"' +
                ' onclick="cyclePriceType(\''+oid+'\')" title="Click to change how this option affects the price">' +
            '<i data-lucide="'+ptIcon+'" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i>' +
            '<span id="optoggle_label_'+oid+'">'+ptLabels[priceType]+'</span>' +
        '</button>' +

        // Price amount
        '<div style="position:relative;min-width:110px;">' +
            '<span style="position:absolute;left:.55rem;top:50%;transform:translateY(-50%);font-size:.75rem;color:var(--text-muted);pointer-events:none;z-index:1;">₱</span>' +
            '<input type="number" class="admin-input" id="oadj_'+oid+'"' +
                ' min="0" step="0.01" value="'+(opt.price_adjustment||0)+'"' +
                ' placeholder="0.00"' +
                (priceType==='none' ? ' disabled' : '') +
                ' style="padding-left:1.4rem;width:110px;">' +
        '</div>' +

        // Default checkbox
        '<label style="display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:var(--text-muted);cursor:pointer;white-space:nowrap;">' +
            '<input type="checkbox" id="odef_'+oid+'" '+(opt.is_default?'checked':'')+
                ' style="accent-color:var(--accent);width:.85rem;height:.85rem;">Default' +
        '</label>' +

        // Active toggle
        '<button type="button" onclick="toggleOptionActive(\''+oid+'\')" id="oact_'+oid+'"' +
                ' class="pill-toggle '+(opt.is_active!==false?'on':'off')+'">' +
            '<span>'+(opt.is_active!==false?'On':'Off')+'</span>' +
        '</button>' +

        // Remove button
        '<button type="button" onclick="removeOption(\''+oid+'\')"' +
                ' style="background:none;border:none;cursor:pointer;color:#ef4444;padding:.15rem;flex-shrink:0;" title="Remove">' +
            '<i data-lucide="x-circle" style="width:.9rem;height:.9rem;stroke-width:2;"></i>' +
        '</button>';

    container.appendChild(row);
    lucide.createIcons();
}

/**
 * Cycle price type: none → add → replace → none
 * Updates the toggle button appearance and shows/hides the price input.
 */
function cyclePriceType(oid) {
    var btn   = document.getElementById('optoggle_' + oid);
    var label = document.getElementById('optoggle_label_' + oid);
    var adj   = document.getElementById('oadj_' + oid);
    if(!btn || !adj) return;

    var cur  = btn.dataset.ptype || 'none';
    var next = cur === 'none' ? 'add' : (cur === 'add' ? 'replace' : 'none');

    var map = {
        none:    { cls:'free',    lbl:'Free (no price change)', ico:'tag',         show:false },
        add:     { cls:'add',     lbl:'+Add to price',          ico:'plus-circle',  show:true  },
        replace: { cls:'replace', lbl:'=Replace price',         ico:'repeat',       show:true  },
    };

    btn.classList.remove('free','add','replace');
    btn.classList.add(map[next].cls);
    btn.dataset.ptype = next;
    if(label) label.textContent = map[next].lbl;

    // Rebuild button inner HTML (Lucide already replaced <i> with <svg>)
    btn.innerHTML =
        '<i data-lucide="'+map[next].ico+'" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i>' +
        '<span id="optoggle_label_'+oid+'">'+map[next].lbl+'</span>';

    adj.disabled      = !map[next].show;
    if(!map[next].show) adj.value = 0;
    lucide.createIcons();
}

function onPriceTypeChange(oid) {
    // kept for compatibility — new UI uses cyclePriceType
    var adj = document.getElementById('oadj_' + oid);
    var btn = document.getElementById('optoggle_' + oid);
    var isNone = (btn ? btn.dataset.ptype : 'none') === 'none';
    adj.style.display = isNone ? 'none' : '';
}

function toggleOptionActive(oid) {
    var btn  = document.getElementById('oact_' + oid);
    var isOn = btn.classList.contains('on');
    btn.classList.replace(isOn ? 'on':'off', isOn ? 'off':'on');
    btn.querySelector('span').textContent = isOn ? 'Off':'On';
}

function removeOption(oid) {
    var row = document.getElementById('optrow_' + oid);
    if(row) row.remove();
}
</script>

<script>
// ─────────────────────────────────────────────────────────
// Serialize groups into hidden inputs before submit
// ─────────────────────────────────────────────────────────
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('itemModalForm');
    if(!form) return;

    form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Handle status field -> is_archived hidden input
    var statusVal = document.getElementById('iStatus').value;
    var existing  = this.querySelector('input[name="is_archived_flag"]');
    if(!existing){ existing = document.createElement('input'); existing.type='hidden'; existing.name='is_archived_flag'; this.appendChild(existing); }
    existing.value = statusVal === 'archived' ? '1':'0';

    // Remove any previously injected group inputs
    this.querySelectorAll('.dyn-group-input').forEach(function(el){ el.remove(); });

    var allCards = document.querySelectorAll('#flavorGroups .group-card, #modifierGroups .group-card');
    var form = this;

    allCards.forEach(function(card, gi) {
        var gid      = card.id.replace('card_','');
        var existId  = card.dataset.existingId || '';
        var type     = card.dataset.type;
        var name     = (document.getElementById('gtitle_'+gid)||{}).value || '';
        var required = (document.getElementById('greq_'+gid)||{}).checked ? '1':'0';
        var isActive = (document.getElementById('gact_'+gid)||{}).classList.contains('on') ? '1':'0';

        function addHidden(n, v) {
            var inp = document.createElement('input');
            inp.type='hidden'; inp.name=n; inp.value=v; inp.className='dyn-group-input';
            form.appendChild(inp);
        }

        if(existId) addHidden('groups['+gi+'][id]',       existId);
        addHidden('groups['+gi+'][type]',      type);
        addHidden('groups['+gi+'][name]',      name);
        addHidden('groups['+gi+'][required]',  required);
        addHidden('groups['+gi+'][is_active]', isActive);

        var optRows = card.querySelectorAll('.option-row');
        optRows.forEach(function(row, oi) {
            var oid      = row.id.replace('optrow_','');
            var oExistId = row.dataset.existingId || '';
            var oName    = (document.getElementById('oname_'+oid)||{}).value || '';
            var oPType   = (function(){ var b = document.getElementById('optoggle_'+oid); return b ? (b.dataset.ptype||'none') : 'none'; })();
            var oAdj     = (document.getElementById('oadj_'+oid)||{}).value  || '0';
            var oDef     = (document.getElementById('odef_'+oid)||{}).checked ? '1':'0';
            var oAct     = (document.getElementById('oact_'+oid)||{}).classList.contains('on') ? '1':'0';

            if(oExistId) addHidden('groups['+gi+'][options]['+oi+'][id]',               oExistId);
            addHidden('groups['+gi+'][options]['+oi+'][name]',              oName);
            addHidden('groups['+gi+'][options]['+oi+'][price_type]',        oPType);
            addHidden('groups['+gi+'][options]['+oi+'][price_adjustment]',  oAdj);
            addHidden('groups['+gi+'][options]['+oi+'][is_default]',        oDef);
            addHidden('groups['+gi+'][options]['+oi+'][is_active]',         oAct);
        });
    });

    // ── Serialize Add-ons ─────────────────────────────────
    form.querySelectorAll('.dyn-addon-input').forEach(function(el){ el.remove(); });

    document.querySelectorAll('#addonRows > div').forEach(function(row, ai) {
        var aid      = row.id.replace('addonrow_','');
        var existId  = row.dataset.existingId || '';
        var aName    = (document.getElementById('adname_' +aid)||{}).value || '';
        var aDesc    = (document.getElementById('addesc_' +aid)||{}).value || '';
        var aAdj     = (document.getElementById('adadj_'  +aid)||{}).value || '0';
        var aAffects = (document.getElementById('adaffects_'+aid)||{}).dataset.affects === '1';
        var aPType   = aAffects ? 'add' : 'none';

        function addAddonHidden(n, v) {
            var inp = document.createElement('input');
            inp.type='hidden'; inp.name=n; inp.value=v; inp.className='dyn-addon-input';
            form.appendChild(inp);
        }

        if(existId) addAddonHidden('addons['+ai+'][id]',               existId);
        addAddonHidden('addons['+ai+'][name]',              aName);
        addAddonHidden('addons['+ai+'][description]',       aDesc);
        addAddonHidden('addons['+ai+'][price_type]',        aPType);
        addAddonHidden('addons['+ai+'][price_adjustment]',  aAdj);
        // Addons are stored as modifier_groups with type='addon'
        addAddonHidden('addons['+ai+'][type]',              'addon');
        addAddonHidden('addons['+ai+'][is_active]',         '1');
    });

    // Now actually submit the form programmatically
    this.submit();
}); // end submit
}); // end DOMContentLoaded
</script>

@endsection
