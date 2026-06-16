(function () {
    'use strict';

    function getMessages() {
        return (window.deleteConfirmMessages || {});
    }

    function buildForm(url, csrfToken) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = csrfToken;
        form.appendChild(csrf);

        var method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        return form;
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-btn-delete');
        if (!btn) {
            return;
        }
        e.preventDefault();

        var url = btn.dataset.url;
        var csrfToken = btn.dataset.csrf;
        var messages = getMessages();

        Swal.fire({
            title: messages.title || 'Are you sure?',
            text: messages.text || 'You want to delete?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: messages.confirm || 'Yes, delete it!',
            cancelButtonText: messages.cancel || 'Cancel',
            reverseButtons: true,
        }).then(function (result) {
            if (result && result.value) {
                buildForm(url, csrfToken).submit();
            }
        });
    });
})();
