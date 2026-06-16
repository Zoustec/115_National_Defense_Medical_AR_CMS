@include('admin._flash')

<div class="form-group">
    <label>{{ __('cms.category_id') }} <span class="text-danger">*</span></label>
    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
        <option value="">--</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $recommendItem->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>{{ __('cms.name') }} <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $recommendItem->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>{{ __('cms.description') }}</label>
    <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $recommendItem->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="hidden" name="status" value="0">
        <input type="checkbox" name="status" value="1" id="status" class="custom-control-input"
               {{ old('status', $recommendItem->status ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="status">{{ __('cms.status') }}</label>
    </div>
</div>
