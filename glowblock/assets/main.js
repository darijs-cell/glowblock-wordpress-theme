(() => {
    'use strict';

    const palette = (window.GlowBlock && window.GlowBlock.palette) || {};
    const hexes = Object.keys(palette).map((k) => palette[k].hex);
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const phaseTimers = new WeakMap();

    /* ---------- Lamp helpers ---------- */

    function setGlow(lamp, hex) {
        lamp.style.setProperty('--glow', hex);
        lamp.dataset.current = hex;
    }

    function stopPhase(lamp) {
        const id = phaseTimers.get(lamp);
        if (id) {
            clearInterval(id);
            phaseTimers.delete(lamp);
        }
    }

    function startPhase(lamp) {
        stopPhase(lamp);
        if (!hexes.length) return;
        let i = Math.max(0, hexes.indexOf(lamp.dataset.current));
        phaseTimers.set(lamp, setInterval(() => {
            i = (i + 1) % hexes.length;
            setGlow(lamp, hexes[i]);
        }, reduceMotion ? 3000 : 1700));
    }

    /* ---------- Pointer tilt on every lamp ---------- */

    document.querySelectorAll('[data-lamp]').forEach((lamp) => {
        lamp.addEventListener('pointermove', (e) => {
            const r = lamp.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            lamp.classList.add('is-hover');
            lamp.style.setProperty('--ry', (-28 + x * 80) + 'deg');
        });
        lamp.addEventListener('pointerleave', () => {
            lamp.classList.remove('is-hover');
            lamp.style.removeProperty('--ry');
        });
    });

    /* ---------- Colour / mode controls ---------- */

    document.querySelectorAll('[data-lamp-scope]').forEach((scope) => {
        const lamp = scope.querySelector('[data-lamp]');
        if (!lamp) return;

        const swatches = scope.querySelectorAll('.swatch');
        const modeButtons = scope.querySelectorAll('.mode');

        const activateSwatch = (hex) => {
            swatches.forEach((s) => s.classList.toggle('is-active', s.dataset.color === hex));
        };

        swatches.forEach((s) => s.addEventListener('click', () => {
            stopPhase(lamp);
            modeButtons.forEach((m) => m.classList.toggle('is-active', m.dataset.mode === 'select'));
            setGlow(lamp, s.dataset.color);
            activateSwatch(s.dataset.color);
        }));

        modeButtons.forEach((m) => m.addEventListener('click', () => {
            modeButtons.forEach((x) => x.classList.toggle('is-active', x === m));
            if (m.dataset.mode === 'phase') {
                startPhase(lamp);
            } else {
                stopPhase(lamp);
            }
        }));

        // keep the active swatch in sync while phasing
        new MutationObserver(() => activateSwatch(lamp.dataset.current))
            .observe(lamp, { attributes: true, attributeFilter: ['data-current'] });
    });

    /* ---------- Product cards: phase on hover ---------- */

    document.querySelectorAll('.lamp-card').forEach((card) => {
        const lamp = card.querySelector('[data-lamp]');
        if (!lamp || reduceMotion) return;
        card.addEventListener('mouseenter', () => startPhase(lamp));
        card.addEventListener('mouseleave', () => {
            stopPhase(lamp);
            setGlow(lamp, lamp.dataset.base);
        });
    });

    /* ---------- Cart (demo only) ---------- */

    const counters = document.querySelectorAll('[data-cart-count]');
    let count = 0;
    try { count = parseInt(sessionStorage.getItem('gbCart') || '0', 10) || 0; } catch (e) { /* storage unavailable */ }

    const renderCount = () => counters.forEach((c) => { c.textContent = count; });
    renderCount();

    let toast;
    function showToast(text) {
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            toast.setAttribute('role', 'status');
            document.body.appendChild(toast);
        }
        toast.textContent = text;
        toast.classList.add('is-visible');
        clearTimeout(showToast.t);
        showToast.t = setTimeout(() => toast.classList.remove('is-visible'), 2200);
    }

    document.querySelectorAll('[data-add-to-cart]').forEach((btn) => btn.addEventListener('click', () => {
        count += 1;
        try { sessionStorage.setItem('gbCart', String(count)); } catch (e) { /* ignore */ }
        renderCount();
        showToast((btn.dataset.name || 'Lamp') + ' added to cart (demo store)');
    }));

    /* ---------- Newsletter (demo only) ---------- */

    document.querySelectorAll('[data-demo-form]').forEach((form) => {
        const msg = form.parentElement.querySelector('.form-msg');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = form.querySelector('input[type="email"]');
            const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
            msg.classList.toggle('error', !ok);
            msg.textContent = ok
                ? 'You are in! (Demo form: nothing was sent.)'
                : 'Please enter a valid email address.';
            if (ok) form.reset();
        });
    });

    /* ---------- Mobile menu ---------- */

    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.getElementById('primary-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
})();
