@props(['name', 'value' => '', 'id' => null])

@php
    $fieldId = $id ?? $name;
@endphp

<textarea name="{{ $name }}" id="{{ $fieldId }}" data-ckeditor="true"
    class="form-control{{ $errors->has($name) ? ' is-invalid' : '' }}">{{ old($name, $value) }}</textarea>

@error($name)
    <small class="text-danger">{{ $message }}</small>
@enderror

@once
    @push('js')
        <script src="{{ asset('js/ckeditor.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-ckeditor]').forEach(function(element) {
                    ClassicEditor
                        .create(element, {
                            toolbar: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'bulletedList', 'numberedList', '|',
                                'blockQuote', 'link', '|',
                                'undo', 'redo',
                            ],
                            heading: {
                                options: [{
                                        model: 'paragraph',
                                        title: 'Paragraph',
                                        class: 'ck-heading_paragraph'
                                    },
                                    {
                                        model: 'heading1',
                                        view: 'h1',
                                        title: 'Heading 1',
                                        class: 'ck-heading_heading1'
                                    },
                                    {
                                        model: 'heading2',
                                        view: 'h2',
                                        title: 'Heading 2',
                                        class: 'ck-heading_heading2'
                                    },
                                    {
                                        model: 'heading3',
                                        view: 'h3',
                                        title: 'Heading 3',
                                        class: 'ck-heading_heading3'
                                    },
                                    {
                                        model: 'heading4',
                                        view: 'h4',
                                        title: 'Heading 4',
                                        class: 'ck-heading_heading4'
                                    },
                                    {
                                        model: 'heading5',
                                        view: 'h5',
                                        title: 'Heading 5',
                                        class: 'ck-heading_heading5'
                                    },
                                    {
                                        model: 'heading6',
                                        view: 'h6',
                                        title: 'Heading 6',
                                        class: 'ck-heading_heading6'
                                    },
                                ],
                            },
                            link: {
                                decorators: {
                                    openInNewTab: {
                                        mode: 'manual',
                                        label: 'Open in a new tab',
                                        attributes: {
                                            target: '_blank',
                                            rel: 'noopener noreferrer',
                                        },
                                    },
                                },
                            },
                        })
                        .then(function(editor) {
                            var form = element.closest('form');
                            if (form) {
                                form.addEventListener('submit', function() {
                                    element.value = editor.getData();
                                });
                            }
                        })
                        .catch(function(error) {
                            console.error('CKEditor initialization error:', error);
                        });
                });
            });
        </script>
    @endpush
@endonce
