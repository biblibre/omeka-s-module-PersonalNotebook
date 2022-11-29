(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.personal-notebook-form').forEach(function (form) {
            form.addEventListener('submit', onSubmit);
        });
    });

    function onSubmit (event) {
        event.preventDefault();

        const form = event.target;
        const url = form.getAttribute('data-url');
        const method = form.getAttribute('data-method');

        const submitter = event.submitter;
        submitter.setAttribute('disabled', '');

        const formData = new FormData(form);
        const body = JSON.stringify({
            'o-module-personal-notebook:resource': {
                'o:id': formData.get('o-module-personal-notebook:resource[o:id]'),
            },
            'o-module-personal-notebook:content': formData.get('o-module-personal-notebook:content'),
        });
        const headers = new Headers({ 'Content-Type': 'application/json' });

        fetch(url, { method, body, headers }).then(function (res) {
            submitter.removeAttribute('disabled');
            if (!res.ok) {
                throw new Error('Error while saving personal note');
            }

            return res.json()
        }).then(function (note) {
            form.setAttribute('data-url', '/personal-notebook/notes/' + note['o:id']);
            form.setAttribute('data-method', 'PUT');

            const ev = new CustomEvent('o-module-personal-notebook:note-save-success', {
                detail: { note },
            });
            form.dispatchEvent(ev);
        }).catch(function (err) {
            const ev = new CustomEvent('o-module-personal-notebook:note-save-error', {
                detail: { err },
            });
            form.dispatchEvent(ev);
        });
    }
})();
