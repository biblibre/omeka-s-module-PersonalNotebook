(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.personal-notebook-form').forEach(function (form) {
            form.addEventListener('submit', onSubmit);

            let resultTimeoutId;
            form.addEventListener('o-module-personal-notebook:note-save-success', function (ev) {
                const resultElement = form.querySelector('.personal-notebook-save-result');
                if (resultElement) {
                    const successElement = document.createElement('span');
                    successElement.classList.add('personal-notebook-save-result-success');
                    successElement.innerHTML = '&check;';
                    resultElement.replaceChildren(successElement);
                    clearTimeout(resultTimeoutId);
                    resultTimeoutId = setTimeout(function () {
                        resultElement.replaceChildren();
                    }, 2000);
                }
            });
            form.addEventListener('o-module-personal-notebook:note-save-error', function (ev) {
                const resultElement = form.querySelector('.personal-notebook-save-result');
                if (resultElement) {
                    const successElement = document.createElement('span');
                    successElement.classList.add('personal-notebook-save-result-error');
                    successElement.innerHTML = '&#x26A0; ' + ev.detail.err;
                    resultElement.replaceChildren(successElement);
                }
            });

            let inputTimeoutId;
            form.elements['o-module-personal-notebook:content'].addEventListener('input', function (ev) {
                const resultElement = form.querySelector('.personal-notebook-save-result');
                if (!resultElement.querySelector('.personal-notebook-save-result-progress')) {
                    const loadingElement = document.createElement('span');
                    loadingElement.classList.add('personal-notebook-save-result-progress');
                    resultElement.replaceChildren(loadingElement);
                }

                clearTimeout(inputTimeoutId);
                inputTimeoutId = setTimeout(() => { form.requestSubmit(); }, 500);
            });
        });
    });

    function onSubmit (event) {
        event.preventDefault();

        const form = event.target;
        const url = form.getAttribute('data-url');
        const method = form.getAttribute('data-method');

        const submitter = event.submitter;
        if (submitter) {
            submitter.setAttribute('disabled', '');
        }

        const formData = new FormData(form);
        const body = JSON.stringify({
            'noteform_csrf': formData.get('noteform_csrf'),
            'o-module-personal-notebook:resource': {
                'o:id': formData.get('o-module-personal-notebook:resource[o:id]'),
            },
            'o-module-personal-notebook:content': formData.get('o-module-personal-notebook:content'),
        });
        const headers = new Headers({ 'Content-Type': 'application/json' });

        fetch(url, { method, body, headers }).then(function (res) {
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
        }).finally(function () {
            if (submitter) {
                submitter.removeAttribute('disabled');
            }
        });
    }
})();
