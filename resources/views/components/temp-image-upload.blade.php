@props([
    'label',
    'multiple'        => false,
    'inputId'         => 'image',
    'inputName'       => null,
    'removeName'      => 'remove_image',
    'deleteName'      => null,
    'existingImages'  => null,
    'hint'            => null,
    'errorKey'        => null,
    'required'        => false,
])

@php
    $inputName ??= $multiple ? 'images[]' : 'image';
    $errorKey  ??= rtrim($inputName, '[]');
@endphp

<div class="form-group">
    <label>{{ $label }} @if ($required) <span class="text-danger">*</span> @endif</label>

    {{-- ── SINGLE MODE ─────────────────────────────────────────────────── --}}
    @if (!$multiple)
        {{-- Image cards (current + new preview side-by-side) --}}
        <div class="d-flex flex-wrap mb-2" style="gap:12px;">

            {{-- Current image (edit page) --}}
            @if ($existingImages)
                <div id="{{ $inputId }}-current-wrap" class="text-center">
                    <span class="d-block text-muted mb-1" style="font-size:11px;line-height:1;">{{ __('common.current_image') }}</span>
                    <div class="position-relative d-inline-block border rounded bg-white"
                         style="width:70px;height:70px;padding:3px;">
                        <img src="{{ Storage::url($existingImages) }}" alt=""
                            id="{{ $inputId }}-current"
                            onerror="this.onerror=null;this.src='{{ asset('img/no-image.svg') }}';"
                            class="w-100 h-100" style="object-fit:contain;">
                        <button type="button" id="btn-remove-{{ $inputId }}"
                            class="btn btn-sm btn-danger position-absolute rounded-circle d-flex align-items-center justify-content-center"
                            style="top:-6px;right:-6px;width:18px;height:18px;padding:0;font-size:.6rem;line-height:1;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="{{ $removeName }}" id="remove_{{ $inputId }}" value="0">
            @endif

            {{-- New preview --}}
            <div id="{{ $inputId }}-preview" class="text-center" style="display:none;">
                <span class="d-block text-muted mb-1" style="font-size:11px;line-height:1;">{{ __('common.new_preview') }}</span>
                <div class="d-inline-block border border-success rounded bg-white"
                     style="width:70px;height:70px;padding:3px;">
                    <img id="{{ $inputId }}-preview-img" src="" alt="Preview"
                        class="w-100 h-100" style="object-fit:contain;">
                </div>
            </div>
        </div>

        {{-- File picker --}}
        <div class="custom-file">
            <input type="file" id="{{ $inputId }}" name="{{ $inputName }}"
                class="custom-file-input @error($errorKey) is-invalid @enderror"
                accept="image/*">
            <label class="custom-file-label text-truncate" for="{{ $inputId }}"
                   data-browse="{{ __('common.choose_file') }}">
                {{ __('common.choose_file') }}
            </label>
        </div>

        @if ($hint)
            <small class="form-text text-muted">{{ $hint }}</small>
        @endif

        @error($errorKey)
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror

        @error($removeName)
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror

    {{-- ── MULTIPLE MODE ───────────────────────────────────────────────── --}}
    @else
        {{-- Existing images grid --}}
        <div id="{{ $inputId }}-grid" class="mb-2 d-flex flex-wrap" style="gap:8px;">

            {{-- Already-saved images (edit page) --}}
            @if ($existingImages)
                @php $deletedOld = array_map('strval', old($deleteName, [])); @endphp
                @foreach ((array) $existingImages as $imgPath)
                    @if (in_array($imgPath, $deletedOld))
                        <input type="hidden" name="{{ $deleteName }}" value="{{ $imgPath }}">
                        @continue
                    @endif
                    <div class="position-relative" data-existing-item style="width:100px;height:100px;">
                        <img src="{{ Storage::url($imgPath) }}" alt=""
                            class="img-thumbnail w-100 h-100" style="object-fit:cover;">
                        <button type="button"
                            class="btn btn-sm btn-danger position-absolute"
                            style="top:2px;right:2px;padding:2px 5px;font-size:.75rem;line-height:1;"
                            onclick="TempUpload.removeExistingItem(this, '{{ $imgPath }}', '{{ $deleteName }}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Hidden file input — accumulates files via DataTransfer --}}
        <input type="file" id="{{ $inputId }}" name="{{ $inputName }}" accept="image/*" multiple
            class="form-control @error($errorKey) is-invalid @enderror">

        @if ($hint)
            <small class="form-text text-muted">{{ $hint }}</small>
        @endif

        @error($errorKey)
            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    @endif
</div>

{{-- Auto-load temp-upload.js once per page --}}
@once
    @push('js')
        <script src="{{ asset('js/admin/temp-upload.js') }}"></script>
    @endpush
@endonce

{{-- Auto-init this upload instance --}}
@push('js')
    <script>
        @if (!$multiple)
            TempUpload.initSingle({
                inputSelector:       '#{{ $inputId }}',
                previewSelector:     '#{{ $inputId }}-preview',
                previewImgSelector:  '#{{ $inputId }}-preview-img',
                currentWrapSelector: '#{{ $inputId }}-current-wrap',
                currentImgSelector:  '#{{ $inputId }}-current',
                removeBtnSelector:   '#btn-remove-{{ $inputId }}',
                removeInputSelector: '#remove_{{ $inputId }}',
            });
        @else
            TempUpload.initMulti({
                inputSelector: '#{{ $inputId }}',
                gridSelector:  '#{{ $inputId }}-grid',
            });
        @endif
    </script>
@endpush
