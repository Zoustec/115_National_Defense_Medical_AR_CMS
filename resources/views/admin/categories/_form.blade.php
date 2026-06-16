@include('admin._flash')

<div class="row">
    <div class="form-group col-md-4">
        <label>{{ __('cms.code') }} <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $category->code ?? '') }}" placeholder="staple / main / fruit">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="form-group col-md-8">
        <label>{{ __('cms.name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $category->name ?? '') }}">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-group">
    <label>{{ __('cms.description') }}</label>
    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
</div>
