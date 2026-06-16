@include('admin._flash')

<div class="row">
    <div class="form-group col-md-6">
        <label>{{ __('cms.category_id') }} <span class="text-danger">*</span></label>
        <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
            <option value="">--</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group col-md-6">
        <label>{{ __('cms.model') }} <span class="text-danger">*</span></label>
        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
               value="{{ old('model', $item->model ?? '') }}" placeholder="e.g. rice, chicken">
        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>{{ __('cms.name') }} <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $item->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<x-temp-image-upload
    :label="__('cms.image')"
    input-id="image"
    :existing-images="$item->image ?? null"
/>

<div class="form-group">
    <label>{{ __('cms.description') }}</label>
    <textarea name="description" rows="2" class="form-control">{{ old('description', $item->description ?? '') }}</textarea>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label>{{ __('cms.unit') }} <span class="text-danger">*</span></label>
        <input type="number" name="unit" min="1" class="form-control @error('unit') is-invalid @enderror"
               value="{{ old('unit', $item->unit ?? 1) }}">
        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('cms.display_order') }} <span class="text-danger">*</span></label>
        <input type="number" name="display_order" min="0" class="form-control @error('display_order') is-invalid @enderror"
               value="{{ old('display_order', $item->display_order ?? 0) }}">
        @error('display_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="status" value="0">
        <input type="checkbox" name="status" value="1" id="status" class="custom-control-input"
               {{ old('status', $item->status ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="status">{{ __('cms.status') }}</label>
    </div>
</div>
