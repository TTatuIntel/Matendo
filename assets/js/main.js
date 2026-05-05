/* =====================================================================
   Matendo Medics — main.js
   Site-wide UI behaviour: nav, scroll-reveal, back-to-top, cookie banner,
   newsletter, smooth-scroll, talent-tabs, sign-in modal.
   ===================================================================== */
(function () {
    'use strict';

    const $  = (s, root = document) => root.querySelector(s);
    const $$ = (s, root = document) => Array.from(root.querySelectorAll(s));
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ------ sticky nav scroll-shadow ------
    const nav = $('#navContainer');
    if (nav) {
        const onScroll = () => nav.classList.toggle('is-scrolled', window.scrollY > 8);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    // ------ scroll-reveal (IntersectionObserver) ------
    if (!reduced && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((en) => {
                if (en.isIntersecting) {
                    en.target.classList.add('is-in');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        $$('[data-reveal], [data-reveal-stagger]').forEach((el) => io.observe(el));
    } else {
        $$('[data-reveal], [data-reveal-stagger]').forEach((el) => el.classList.add('is-in'));
    }

    // ------ smooth scroll for in-page anchors ------
    $$('a[href^="#"]').forEach((a) => {
        const href = a.getAttribute('href');
        if (!href || href === '#') return;
        a.addEventListener('click', (ev) => {
            const target = document.querySelector(href);
            if (!target) return;
            ev.preventDefault();
            target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
        });
    });

    // ------ mobile menu ------
    const menuBtn = $('#mobileMenuBtn');
    const menu    = $('#mobileMenu');
    if (menuBtn && menu) {
        const setOpen = (open) => {
            menu.hidden = !open;
            menuBtn.setAttribute('aria-expanded', String(open));
            const icon = menuBtn.firstElementChild;
            if (icon) {
                icon.classList.toggle('fa-bars', !open);
                icon.classList.toggle('fa-times', open);
            }
            document.body.style.overflow = open ? 'hidden' : '';
        };
        menuBtn.addEventListener('click', () => setOpen(menu.hidden));
        menu.addEventListener('click', (e) => { if (e.target.tagName === 'A') setOpen(false); });
        window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !menu.hidden) setOpen(false); });
    }

    // ------ talent tabs (horizontal specialty tabs) ------
    const tabs = $$('.talent-tab');
    const panels = $$('[data-talent-panel]');
    if (tabs.length && panels.length) {
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.talentTab;
                tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
                panels.forEach((p) => {
                    p.hidden = p.dataset.talentPanel !== target;
                    p.setAttribute('aria-hidden', p.hidden ? 'true' : 'false');
                });
            });
        });
    }

    // ------ back to top ------
    const top = $('#backToTop');
    if (top) {
        window.addEventListener('scroll', () => { top.hidden = window.scrollY < 400; }, { passive: true });
        top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' }));
    }

    // ------ cookie banner ------
    const banner   = $('#cookieConsent');
    const accept   = $('#acceptCookies');
    const decline  = $('#declineCookies');
    if (banner) {
        const stored = localStorage.getItem('cookieConsent');
        if (!stored) banner.hidden = false;
        accept?.addEventListener('click', () => { localStorage.setItem('cookieConsent', 'accepted'); banner.hidden = true; });
        decline?.addEventListener('click', () => { localStorage.setItem('cookieConsent', 'declined'); banner.hidden = true; });
    }

    // ------ generic modal openers/closers (works site-wide) ------
    document.addEventListener('click', (ev) => {
        const opener = ev.target.closest('[data-open-modal]');
        if (opener) {
            ev.preventDefault();
            const id = opener.getAttribute('data-open-modal');
            const m  = document.getElementById(id);
            if (m) {
                m.hidden = false;
                document.body.style.overflow = 'hidden';
                m.querySelector('input,select,textarea,button')?.focus();
            }
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

    // ------ newsletter ------
    const nl = $('#newsletterForm');
    if (nl) wireJsonForm(nl, 'api/newsletter.php', { successAlert: true });

    // ------ contact form ------
    const cf = $('#contactForm');
    if (cf) wireJsonForm(cf, 'api/contact.php');

    // ------ login modal form ------
    const lf = $('#loginForm');
    if (lf) {
        wireJsonForm(lf, 'api/login.php', {
            onSuccess: () => { setTimeout(() => window.location.reload(), 600); },
        });
    }

    // ------ register modal form ------
    const rf = $('#registerForm');
    if (rf) {
        wireJsonForm(rf, 'api/register.php', {
            onSuccess: () => { setTimeout(() => window.location.reload(), 800); },
        });
    }

    function wireJsonForm(form, endpoint, opts = {}) {
        const fb = form.querySelector('[data-feedback]');
        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            if (!form.reportValidity()) return;

            if (fb) { fb.hidden = true; fb.classList.remove('success', 'error'); }
            const submit = form.querySelector('button[type=submit]');
            submit.disabled = true;
            const original = submit.textContent;
            submit.textContent = 'Sending…';

            try {
                const res  = await fetch(endpoint, {
                    method: 'POST',
                    body:   new FormData(form),
                    headers: { 'X-CSRF-TOKEN': csrf() },
                    credentials: 'same-origin',
                });
                const json = await res.json().catch(() => ({}));
                if (fb) {
                    fb.hidden = false;
                    fb.classList.add(json.success ? 'success' : 'error');
                    fb.textContent = json.message || json.error || 'Done.';
                }
                if (json.success) {
                    form.reset();
                    if (opts.successAlert) alert(json.message || 'Done.');
                    if (opts.onSuccess) opts.onSuccess(json);
                }
            } catch (e) {
                if (fb) { fb.hidden = false; fb.classList.add('error'); fb.textContent = 'Network error. Please try again.'; }
            } finally {
                submit.disabled = false;
                submit.textContent = original;
            }
        });
    }
})();
