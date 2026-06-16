{{-- Variables expected:
     - $learningUnit (nullable on create)
     - $items (collection, grouped by category in view)
     - $recommendItems (collection)
     - $selectedItems  (keyed by item_id, LearningUnitItem)
     - $selectedRecommends (keyed by recommend_item_id, LearningUnitRecommendItem)
--}}
@php
    $selectedItems = $selectedItems ?? collect();
    $selectedRecommends = $selectedRecommends ?? collect();
    $appObjects = old('applicable_objects', isset($learningUnit) ? implode(', ', $learningUnit->applicable_objects ?? []) : '');

    $itemCategories = $items->pluck('category')->filter()->unique('id')->sortBy('name')->values();
    $recCategories  = $recommendItems->pluck('category')->filter()->unique('id')->sortBy('name')->values();

    $pageSize = 20;
@endphp

@include('admin._flash')

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-basic">{{ __('cms.tab_basic') }}</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-items">{{ __('cms.tab_items') }}</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-recommends">{{ __('cms.tab_replacements') }}</a></li>
</ul>

<div class="tab-content pt-4">

    {{-- ── Tab 1: basic ─────────────────────────────────────────── --}}
    <div id="tab-basic" class="tab-pane fade show active">
        <div class="row">
            <div class="form-group col-md-8">
                <label>{{ __('cms.name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $learningUnit->name ?? '') }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group col-md-4">
                <label>{{ __('cms.code') }} <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                       value="{{ old('code', $learningUnit->code ?? '') }}" maxlength="50">
                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <x-temp-image-upload
            :label="__('cms.image')"
            input-id="image"
            :existing-images="$learningUnit->image ?? null"
        />

        <div class="form-group">
            <label>{{ __('cms.description') }}</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $learningUnit->description ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>{{ __('cms.applicable_objects') }}</label>
            <input type="text" name="applicable_objects" class="form-control"
                   value="{{ $appObjects }}" placeholder="糖尿病患者, 血糖值異常">
        </div>

        <div class="form-group">
            <label>{{ __('cms.dietary_recommendation_title') }}</label>
            <input type="text" name="dietary_recommendation_title" class="form-control"
                   value="{{ old('dietary_recommendation_title', $learningUnit->dietary_recommendation_title ?? '') }}"
                   maxlength="255">
        </div>

        <div class="form-group">
            <label>{{ __('cms.dietary_recommendations') }}</label>
            <x-ckeditor name="dietary_recommendations" :value="$learningUnit->dietary_recommendations ?? ''" />
        </div>

        <div class="form-group">
            <label>{{ __('cms.clinical_note_title') }}</label>
            <input type="text" name="clinical_note_title" class="form-control"
                   value="{{ old('clinical_note_title', $learningUnit->clinical_note_title ?? '') }}"
                   maxlength="255">
        </div>

        <div class="form-group">
            <label>{{ __('cms.clinical_notes') }}</label>
            <x-ckeditor name="clinical_notes" :value="$learningUnit->clinical_notes ?? ''" />
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" id="status" class="custom-control-input"
                           {{ old('status', $learningUnit->status ?? false) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="status">{{ __('cms.status') }}</label>
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_locked" value="0">
                    <input type="checkbox" name="is_locked" value="1" id="is_locked" class="custom-control-input"
                           {{ old('is_locked', $learningUnit->is_locked ?? false) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_locked">{{ __('cms.is_locked') }}</label>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tab 2: items selection ───────────────────────────────── --}}
    <div id="tab-items" class="tab-pane fade" data-picker data-page-size="{{ $pageSize }}">
        <p class="text-muted">{{ __('cms.items_help') }}</p>

        <div class="form-row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm"
                       data-role="search" placeholder="{{ __('cms.search_placeholder') }}">
            </div>
            <div class="col-md-3">
                <select class="form-control form-control-sm" data-role="category-filter">
                    <option value="">{{ __('cms.filter_all_categories') }}</option>
                    @foreach ($itemCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="d-inline-flex align-items-center mb-0">
                    <input type="checkbox" class="mr-1" data-role="selected-only">
                    <small>{{ __('cms.show_selected_only') }}</small>
                </label>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                        data-role="clear-filters" title="{{ __('cms.clear_filters') }}">×</button>
            </div>
        </div>

        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('cms.include') }}</th>
                    <th>{{ __('cms.default') }}</th>
                    <th>{{ __('cms.category_id') }}</th>
                    <th>{{ __('cms.model') }}</th>
                    <th>{{ __('cms.name') }}</th>
                </tr>
            </thead>
            <tbody data-role="rows">
                @foreach ($items as $it)
                    @php
                        $isSelected = $selectedItems->has($it->id);
                        $isDefault  = $isSelected && (bool) $selectedItems[$it->id]->is_default;
                    @endphp
                    <tr data-role="row"
                        data-name="{{ Str::lower($it->name) }}"
                        data-category-id="{{ $it->category_id }}"
                        data-selected="{{ $isSelected ? '1' : '0' }}">
                        <td><input type="checkbox" name="item_ids[]" value="{{ $it->id }}"
                                   data-role="toggle" {{ $isSelected ? 'checked' : '' }}></td>
                        <td><input type="checkbox" name="default_item_ids[]" value="{{ $it->id }}" {{ $isDefault ? 'checked' : '' }}></td>
                        <td>{{ $it->category?->name }}</td>
                        <td><code>{{ $it->model }}</code></td>
                        <td>{{ $it->name }}</td>
                    </tr>
                @endforeach
                <tr data-role="empty" class="d-none">
                    <td colspan="5" class="text-center text-muted">{{ __('cms.no_results') }}</td>
                </tr>
            </tbody>
        </table>

        @include('admin.learning-units._pager')
    </div>

    {{-- ── Tab 3: recommend items + column/weight/unit_text ─────── --}}
    <div id="tab-recommends" class="tab-pane fade" data-picker data-page-size="{{ $pageSize }}">
        <p class="text-muted">{{ __('cms.replacements_help') }}</p>

        <div class="form-row mb-2">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm"
                       data-role="search" placeholder="{{ __('cms.search_placeholder') }}">
            </div>
            <div class="col-md-3">
                <select class="form-control form-control-sm" data-role="category-filter">
                    <option value="">{{ __('cms.filter_all_categories') }}</option>
                    @foreach ($recCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="d-inline-flex align-items-center mb-0">
                    <input type="checkbox" class="mr-1" data-role="selected-only">
                    <small>{{ __('cms.show_selected_only') }}</small>
                </label>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                        data-role="clear-filters" title="{{ __('cms.clear_filters') }}">×</button>
            </div>
        </div>

        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('cms.enable') }}</th>
                    <th>{{ __('cms.category_id') }}</th>
                    <th>{{ __('cms.name') }}</th>
                    <th>{{ __('cms.column') }}</th>
                    <th>{{ __('cms.weight') }}</th>
                    <th>{{ __('cms.unit_text') }}</th>
                </tr>
            </thead>
            <tbody data-role="rows">
                @foreach ($recommendItems as $rec)
                    @php
                        $sel = $selectedRecommends->get($rec->id);
                        $enabled = (bool) $sel;
                    @endphp
                    <tr data-role="row"
                        data-name="{{ Str::lower($rec->name) }}"
                        data-category-id="{{ $rec->category_id }}"
                        data-selected="{{ $enabled ? '1' : '0' }}">
                        <td>
                            <input type="checkbox" name="recommends[{{ $rec->id }}][enabled]" value="1"
                                   data-role="toggle" {{ $enabled ? 'checked' : '' }}>
                        </td>
                        <td>{{ $rec->category?->name }}</td>
                        <td>{{ $rec->name }}</td>
                        <td>
                            {{-- Replacement group follows the item's category and is no longer editable here --}}
                            <input type="hidden" name="recommends[{{ $rec->id }}][column]" value="{{ $rec->category_id }}">
                            <span class="text-muted">{{ $rec->category?->name }}</span>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                   name="recommends[{{ $rec->id }}][weight]" value="{{ $sel?->weight }}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                   name="recommends[{{ $rec->id }}][unit_text]" value="{{ $sel?->unit_text }}"
                                   placeholder="公克 (1/2片)">
                        </td>
                    </tr>
                @endforeach
                <tr data-role="empty" class="d-none">
                    <td colspan="6" class="text-center text-muted">{{ __('cms.no_results') }}</td>
                </tr>
            </tbody>
        </table>

        @include('admin.learning-units._pager')
    </div>
</div>

@push('js')
<script>
(function () {
    document.querySelectorAll('[data-picker]').forEach(function (root) {
        const pageSize = parseInt(root.dataset.pageSize, 10) || 20;
        const search   = root.querySelector('[data-role="search"]');
        const filter   = root.querySelector('[data-role="category-filter"]');
        const selOnly  = root.querySelector('[data-role="selected-only"]');
        const clearBtn = root.querySelector('[data-role="clear-filters"]');
        const rows     = Array.from(root.querySelectorAll('[data-role="row"]'));
        const emptyRow = root.querySelector('[data-role="empty"]');
        const pager    = root.querySelector('[data-role="pager"]');
        const pageInfo = root.querySelector('[data-role="page-info"]');
        const prevBtn  = root.querySelector('[data-role="prev"]');
        const nextBtn  = root.querySelector('[data-role="next"]');

        let currentPage = 1;
        let filtered = rows.slice();

        rows.forEach(function (row) {
            const cb = row.querySelector('[data-role="toggle"]');
            if (!cb) return;
            cb.addEventListener('change', function () {
                row.dataset.selected = cb.checked ? '1' : '0';
                if (selOnly && selOnly.checked) render();
            });
        });

        function applyFilter() {
            const q = (search.value || '').trim().toLowerCase();
            const catId = filter.value;
            const onlySel = selOnly ? selOnly.checked : false;
            filtered = rows.filter(function (row) {
                if (q && !row.dataset.name.includes(q)) return false;
                if (catId && row.dataset.categoryId !== catId) return false;
                if (onlySel && row.dataset.selected !== '1') return false;
                return true;
            });
            currentPage = 1;
            render();
        }

        function render() {
            const total = filtered.length;
            rows.forEach(function (row) { row.classList.add('d-none'); });

            if (total === 0) {
                emptyRow.classList.remove('d-none');
                if (pager) pager.classList.add('d-none');
                return;
            }
            emptyRow.classList.add('d-none');

            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * pageSize;
            filtered.slice(start, start + pageSize).forEach(function (row) {
                row.classList.remove('d-none');
            });

            if (pager) {
                if (total > pageSize) {
                    pager.classList.remove('d-none');
                    pageInfo.textContent = currentPage + ' / ' + totalPages + ' (' + total + ')';
                    prevBtn.disabled = currentPage <= 1;
                    nextBtn.disabled = currentPage >= totalPages;
                } else {
                    pager.classList.add('d-none');
                }
            }
        }

        search.addEventListener('input', applyFilter);
        filter.addEventListener('change', applyFilter);
        if (selOnly) selOnly.addEventListener('change', applyFilter);
        clearBtn.addEventListener('click', function () {
            search.value = '';
            filter.value = '';
            if (selOnly) selOnly.checked = false;
            applyFilter();
        });
        if (prevBtn) prevBtn.addEventListener('click', function () {
            if (currentPage > 1) { currentPage--; render(); }
        });
        if (nextBtn) nextBtn.addEventListener('click', function () {
            currentPage++;
            render();
        });

        applyFilter();
    });
})();
</script>
@endpush
