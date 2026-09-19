// Public-site behaviour. Everything here is progressive: the HTML already shows final values.

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Run `callback` once, when `el` is ~25% on screen.
function onceInView(el, callback) {
    if (!('IntersectionObserver' in window)) return callback();
    const io = new IntersectionObserver((entries) => {
        if (entries.some((e) => e.isIntersecting)) {
            io.disconnect();
            callback();
        }
    }, { threshold: 0.25 });
    io.observe(el);
}

// Proof strip: one shared 1.5s ease-out clock drives every counter.
// Only the first number in each value animates; the rest ("+", "From ") stays put.
function initCounters(strip) {
    const items = [...strip.querySelectorAll('[data-count]')].map((el) => {
        const text = el.textContent.trim();
        const match = text.match(/[\d,]+/);
        if (!match) return null;
        const to = parseInt(match[0].replace(/,/g, ''), 10);
        const grouped = match[0].includes(',');
        return { el, text, match: match[0], from: Number(el.dataset.from ?? 0), to, grouped };
    }).filter(Boolean);

    if (!items.length || reducedMotion) return;

    const render = (t) => items.forEach((c) => {
        const n = Math.round(c.from + (c.to - c.from) * t);
        c.el.textContent = c.text.replace(c.match, c.grouped ? n.toLocaleString('en-IN') : String(n));
    });

    render(0);
    onceInView(strip, () => {
        const start = performance.now();
        const tick = (now) => {
            const p = Math.min(1, (now - start) / 1500);
            render(1 - Math.pow(1 - p, 3));
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
}

document.querySelectorAll('[data-counters]').forEach(initCounters);

// Draw-on animations (export arcs) start when their element scrolls into view.
document.querySelectorAll('[data-inview]').forEach((el) => onceInView(el, () => el.classList.add('is-inview')));

// Hero video: skip it for reduced motion and data-saver users; the poster stays.
document.querySelectorAll('[data-hero-video]').forEach((video) => {
    if (reducedMotion || navigator.connection?.saveData) {
        video.removeAttribute('autoplay');
        video.pause();
        return;
    }
    video.play().catch(() => {});
});

// Conversion tracking: any element with data-track sends a GA4 event when clicked.
document.addEventListener('click', (e) => {
    const el = e.target.closest('[data-track]');
    if (el && typeof window.gtag === 'function') {
        window.gtag('event', 'generate_lead_click', { cta: el.dataset.track, link_url: el.href ?? '' });
    }
});
