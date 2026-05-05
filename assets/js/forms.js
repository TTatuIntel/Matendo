/* =====================================================================
   Matendo Medics — forms.js
   Modal openers, conditional sections, multipart submission for hire/join.
   ===================================================================== */
(function () {
    'use strict';

    const $$ = (s, r = document) => r.querySelectorAll(s);
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ---------- modal open/close ----------
    document.addEventListener('click', (ev) => {
        const opener = ev.target.closest('[data-open-modal]');
        if (opener) {
            const id = opener.getAttribute('data-open-modal');
            const m  = document.getElementById(id);
            if (m) { m.hidden = false; document.body.style.overflow = 'hidden'; }
        }
        if (ev.target.closest('[data-close-modal]') || ev.target.classList.contains('modal')) {
            const m = ev.target.closest('.modal');
            if (m) { m.hidden = true; document.body.style.overflow = ''; }
        }
    });

    document.addEventListener('keydown', (ev) => {
        if (ev.key === 'Escape') {
            $$('.modal:not([hidden])').forEach(m => { m.hidden = true; document.body.style.overflow = ''; });
        }
    });

    // ---------- conditional sections (data-show-when="name=value") ----------
    $$('[data-show-when]').forEach(el => el.style.display = 'none');
    document.addEventListener('change', (ev) => {
        if (!ev.target.matches('input[type=radio]')) return;
        const name = ev.target.name;
        $$('[data-show-when]').forEach(section => {
            const [n, v] = section.getAttribute('data-show-when').split('=');
            if (n === name) {
                section.style.display = (ev.target.value === v && ev.target.checked) ? '' : 'none';
            }
        });
    });

    // ---------- secure form submission ----------
    $$('form[data-endpoint]').forEach(form => {
        const endpoint = form.getAttribute('data-endpoint');
        const fb       = form.querySelector('[data-feedback]');
        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            if (!form.reportValidity()) return;

            if (fb) { fb.hidden = true; fb.classList.remove('success', 'error'); }

            const submit = form.querySelector('button[type=submit]');
            const original = submit.textContent;
            submit.disabled = true; submit.textContent = 'Submitting…';

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    body:   new FormData(form),
                    headers: { 'X-CSRF-TOKEN': csrf() },
                    credentials: 'same-origin',
                });
                const json = await res.json().catch(() => ({}));
                if (fb) {
                    fb.hidden = false;
                    fb.classList.add(json.success ? 'success' : 'error');
                    if (json.success) {
                        fb.textContent = (json.message || 'Submitted.') + (json.reference ? ' Reference: ' + json.reference : '');
                        form.reset();
                        // close enclosing modal if any
                        const m = form.closest('.modal');
                        if (m) setTimeout(() => { m.hidden = true; document.body.style.overflow = ''; }, 1500);
                    } else if (json.errors) {
                        fb.textContent = 'Please correct the highlighted fields.';
                        Object.entries(json.errors).forEach(([k, msg]) => {
                            const input = form.querySelector(`[name="${k}"]`);
                            if (input) input.setCustomValidity(msg);
                        });
                        form.reportValidity();
                    } else {
                        fb.textContent = json.error || 'Could not submit. Please try again.';
                    }
                }
            } catch (e) {
                if (fb) { fb.hidden = false; fb.classList.add('error'); fb.textContent = 'Network error. Please try again.'; }
            } finally {
                submit.disabled = false; submit.textContent = original;
            }
        });

        // Clear custom validity on edit.
        form.addEventListener('input', ev => ev.target.setCustomValidity?.(''));
    });
})();
