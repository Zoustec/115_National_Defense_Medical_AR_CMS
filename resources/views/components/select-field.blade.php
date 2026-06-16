@props([
    'name',
    'label',
    'options',
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'labelAttribute' => 'name',
    'multiple' => false,
])

<div class="form-group">
    <label for="{{ $name }}">
        {!! $label !!}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    @if ($multiple)
        <select name="{{ $name }}[]" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror" multiple>
            @foreach ($options as $option)
                <option value="{{ $option->id }}"
                    {{ in_array((string) $option->id, array_map('strval', (array) old($name, $selected))) ? 'selected' : '' }}>
                    {{ $option->{$labelAttribute} }}
                </option>
            @endforeach
        </select>
    @else
        <select name="{{ $name }}" id="{{ $name }}" class="form-control @error($name) is-invalid @enderror">
            @if ($placeholder)
                <option value="">— {{ $placeholder }} —</option>
            @endif
            @foreach ($options as $option)
                <option value="{{ $option->id }}"
                    {{ (string) old($name, $selected) === (string) $option->id ? 'selected' : '' }}>
                    {{ $option->{$labelAttribute} }}
                </option>
            @endforeach
        </select>
    @endif

    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>
