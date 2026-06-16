/**
 * Client-side file picker helpers. No server upload — files are previewed
 * locally and submitted together with the form.
 *
 *   TempUpload.initSingle(options) — single-file picker (e.g. logo)
 *   TempUpload.initMulti(options)  — multi-file picker  (e.g. gallery)
 *   TempUpload.removeExistingItem(btn, imgPath, deleteName) — mark persisted item for deletion
 */
var TempUpload = (function ($) {
    'use strict';

    function initSingle(opts) {
        var o = $.extend({
            inputSelector:       '#upload-file',
            previewSelector:     '#file-preview',
            previewImgSelector:  '#preview-img',
            currentWrapSelector: '#current-file-wrap',
            currentImgSelector:  '#current-file',
            removeBtnSelector:   '#btn-remove-file',
            removeInputSelector: '#remove_file',
        }, opts);

        var $input = $(o.inputSelector);
        if (!$input.length) return;

        var currentPreviewUrl = null;

        $input.on('change', function () {
            var file = this.files && this.files[0];

            if (currentPreviewUrl) {
                URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = null;
            }

            if (!file) {
                $(o.previewSelector).hide();
                $(o.currentImgSelector).css('opacity', '1');
                $input.next('.custom-file-label').text(
                    $input.next('.custom-file-label').data('browse') || 'Choose file'
                );
                return;
            }

            $input.next('.custom-file-label').html(file.name);

            currentPreviewUrl = URL.createObjectURL(file);
            $(o.previewImgSelector).attr('src', currentPreviewUrl);
            $(o.previewSelector).show();
            $(o.removeInputSelector).val('0');
            $(o.currentWrapSelector).show();
            $(o.currentImgSelector).css('opacity', '0.5');
        });

        $(o.removeBtnSelector).on('click', function () {
            $(o.removeInputSelector).val('1');
            $(o.currentWrapSelector).hide();

            if (currentPreviewUrl) {
                URL.revokeObjectURL(currentPreviewUrl);
                currentPreviewUrl = null;
            }
            $(o.previewSelector).hide();

            $input.val('');
            $input.next('.custom-file-label').text(
                $input.next('.custom-file-label').data('browse') || 'Choose file'
            );
        });
    }

    function initMulti(opts) {
        var o = $.extend({
            inputSelector: '#multi-picker',
            gridSelector:  '#multi-images-grid',
        }, opts);

        var $input = $(o.inputSelector);
        if (!$input.length) return;

        // Accumulator so multiple "Choose files" rounds add up instead of replacing.
        var bag = new DataTransfer();

        function syncInput() {
            $input[0].files = bag.files;
        }

        function buildItem(file, index) {
            var url = URL.createObjectURL(file);
            var $item = $('<div>', {
                'class': 'position-relative',
                'data-temp-item': '',
                'data-index': index,
                css: { width: '100px', height: '100px' }
            });
            $item.append(
                $('<img>', {
                    src: url,
                    'class': 'img-thumbnail w-100 h-100',
                    css: { objectFit: 'cover' }
                }),
                $('<button>', {
                    type: 'button',
                    'class': 'btn btn-sm btn-danger position-absolute',
                    css: { top: '2px', right: '2px', padding: '2px 5px', fontSize: '.75rem', lineHeight: '1' },
                    click: function () {
                        URL.revokeObjectURL(url);
                        var removeIdx = parseInt($item.attr('data-index'), 10);
                        $item.remove();
                        rebuildBag(removeIdx);
                    }
                }).append($('<i>', { 'class': 'fas fa-times' }))
            );
            return $item;
        }

        function rebuildBag(removeIdx) {
            var next = new DataTransfer();
            for (var i = 0; i < bag.files.length; i++) {
                if (i !== removeIdx) next.items.add(bag.files[i]);
            }
            bag = next;
            syncInput();
            // Re-index remaining DOM items so subsequent removals stay aligned.
            $(o.gridSelector).find('[data-temp-item]').each(function (i) {
                $(this).attr('data-index', i);
            });
        }

        $input.on('change', function () {
            var files = Array.prototype.slice.call(this.files);
            if (!files.length) return;

            var $grid = $(o.gridSelector);
            files.forEach(function (file) {
                bag.items.add(file);
                $grid.append(buildItem(file, bag.files.length - 1));
            });
            syncInput();
        });
    }

    function removeExistingItem(btn, imgPath, deleteName) {
        var $btn = $(btn);
        $btn.closest('form').append(
            $('<input>', { type: 'hidden', name: deleteName, value: imgPath })
        );
        $btn.closest('[data-existing-item]').remove();
    }

    return {
        initSingle:         initSingle,
        initMulti:          initMulti,
        removeExistingItem: removeExistingItem,
    };
})(jQuery);
