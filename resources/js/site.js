import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import registerShop from './shop';

Alpine.plugin(collapse);

/**
 * Shared behaviour for every generated website:
 *  - mobile navigation & dropdowns (x-data="siteNav")
 *  - header that changes style once the page is scrolled
 *  - simple gallery lightbox (x-data="lightbox")
 */
Alpine.data('siteNav', () => ({
    open: false,
    scrolled: false,
    init() {
        const update = () => (this.scrolled = window.scrollY > 24);
        update();
        window.addEventListener('scroll', update, { passive: true });
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('lightbox', () => ({
    current: null,
    show(src, title = '') {
        this.current = { src, title };
    },
    hide() {
        this.current = null;
    },
}));

/**
 * Lightweight motion for composer templates (no dependencies):
 *  - [data-reveal]       fade/slide/zoom in when scrolled into view
 *  - [data-count]        count up numbers ("1.200+", "98%") when visible
 *  - [data-parallax=".2"] subtle parallax on scroll
 * Everything is skipped when the user prefers reduced motion.
 */
function initMotion() {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const reveal = document.querySelectorAll('[data-reveal]');
    const counters = document.querySelectorAll('[data-count]');

    if (reduce || !('IntersectionObserver' in window)) {
        reveal.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            if (entry.target.dataset.count !== undefined) countUp(entry.target);
            io.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    reveal.forEach((el) => io.observe(el));
    counters.forEach((el) => io.observe(el));

    // Safety net: never leave content hidden (e.g. print, very tall screens).
    setTimeout(() => reveal.forEach((el) => el.classList.add('is-visible')), 4000);

    const parallax = [...document.querySelectorAll('[data-parallax]')];
    if (parallax.length) {
        let ticking = false;
        const update = () => {
            parallax.forEach((el) => {
                const rect = el.getBoundingClientRect();
                const speed = parseFloat(el.dataset.parallax) || 0.15;
                el.style.transform = `translate3d(0, ${(rect.top + rect.height / 2 - innerHeight / 2) * -speed}px, 0)`;
            });
            ticking = false;
        };
        addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(update);
                ticking = true;
            }
        }, { passive: true });
        update();
    }
}

function countUp(el) {
    const raw = el.textContent.trim();
    const match = raw.match(/^([^\d]*)([\d.,]+)(.*)$/);
    if (!match) return;
    const [, prefix, number, suffix] = match;
    const thousands = number.includes('.') && number.split('.').pop().length === 3;
    const target = parseFloat(thousands ? number.replace(/\./g, '') : number.replace(',', '.'));
    if (!isFinite(target)) return;
    const decimals = !thousands && number.includes('.') ? number.split('.').pop().length : 0;
    const start = performance.now();
    const duration = 1400;
    const format = (n) => (thousands ? Math.round(n).toLocaleString('id-ID') : n.toFixed(decimals));
    const step = (now) => {
        const p = Math.min(1, (now - start) / duration);
        el.textContent = prefix + format(target * (1 - Math.pow(1 - p, 3))) + suffix;
        if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

registerShop(Alpine);

window.Alpine = Alpine;
Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMotion);
} else {
    initMotion();
}
