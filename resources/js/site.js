import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

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

window.Alpine = Alpine;
Alpine.start();
