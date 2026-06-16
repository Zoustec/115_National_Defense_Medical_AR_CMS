@props([
    'name',
    'label',
    'options',
    'selected' => [],
    'placeholder' => null,
    'required' => false,
    'labelAttribute' => 'name',
    'valueAttribute' => 'id',
])

@php
    $selectedIds = array_map('strval', (array) old($name, $selected));
    $fieldId = 'select2_' . Str::slug($name, '_');
@endphp

<div class="form-group">
    <label for="{{ $fieldId }}">
        {!! $label !!}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select name="{{ $name }}[]"
            id="{{ $fieldId }}"
            class="form-control select2-multi-field @error($name) is-invalid @enderror"
            multiple
            data-placeholder="{{ $placeholder ?? '' }}">
        @foreach ($options as $option)
            <option value="{{ $option->{$valueAttribute} }}"
                {{ in_array((string) $option->{$valueAttribute}, $selectedIds) ? 'selected' : '' }}>
                {{ $option->{$labelAttribute} }}
            </option>
        @endforeach
    </select>
    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

@once
    @push('css')
        <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
        <style>
            .select2-container--default .select2-selection--multiple {
                border: 1px solid #ced4da;
                border-radius: 0.25rem;
                min-height: 38px;
                padding: 2px 4px;
            }
            .select2-container--default.select2-container--focus .select2-selection--multiple {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            .select2-container { width: 100% !important; }
        </style>
    @endpush

    @push('js')
        <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.select2-multi-field').forEach(function (el) {
                    $(el).select2({
                        placeholder: el.dataset.placeholder || '',
                        allowClear: true,
                        width: '100%'
                    });
                });
            });
        </script>
    @endpush
@endonce
