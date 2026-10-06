import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Sortable from 'sortablejs';
import 'trix';

window.Sortable = Sortable;

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function postJson(url, data, method = 'POST') {
    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    });

    if (!response.ok) {
        throw new Error(`Request failed: ${response.status}`);
    }

    return response.json();
}

window.postJson = postJson;

// Disable file attachments in Trix (uploads go through the media library).
document.addEventListener('trix-file-accept', (event) => event.preventDefault());

/**
 * Autosave the text fields of a form (wizard/editor tabs).
 * Usage: <form x-data="autosave('{{ route(...) }}', 'info')">
 */
Alpine.data('autosave', (url, tab) => ({
    status: 'idle',
    savedAt: null,
    timer: null,
    init() {
        const schedule = () => {
            clearTimeout(this.timer);
            this.status = 'pending';
            this.timer = setTimeout(() => this.save(), 1200);
        };
        this.$el.addEventListener('input', (e) => {
            if (e.target.type !== 'file') schedule();
        });
        this.$el.addEventListener('trix-change', schedule);
    },
    async save() {
        const form = new FormData(this.$el);
        const data = { _tab: tab };
        for (const [key, value] of form.entries()) {
            if (value instanceof File || key.startsWith('_') || key.endsWith('_media') || key.endsWith('_remove')) continue;
            // Support nested names: a[b] and a[0][b]
            const parts = key.replace(/\]/g, '').split('[');
            let target = data;
            parts.slice(0, -1).forEach((part) => {
                target[part] = target[part] || {};
                target = target[part];
            });
            target[parts[parts.length - 1]] = value;
        }
        this.status = 'saving';
        try {
            const result = await postJson(url, data);
            this.status = 'saved';
            this.savedAt = result.at;
        } catch (e) {
            this.status = 'error';
        }
    },
}));

/**
 * Drag & drop ordering of a flat list. Posts ids in order to `url`.
 */
Alpine.data('sortableList', (url) => ({
    saving: false,
    init() {
        Sortable.create(this.$refs.list, {
            handle: '[data-handle]',
            animation: 150,
            onEnd: async () => {
                const ids = [...this.$refs.list.querySelectorAll('[data-id]')].map((el) => Number(el.dataset.id));
                this.saving = true;
                try {
                    await postJson(url, { ids });
                } finally {
                    this.saving = false;
                }
            },
        });
    },
}));

/**
 * Nested (2 level) drag & drop menu builder.
 */
Alpine.data('menuBuilder', (url) => ({
    status: 'idle',
    init() {
        const options = {
            group: 'menu',
            handle: '[data-handle]',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: () => this.save(),
            onMove: (evt) => {
                // Items with sub menus cannot be nested (max 2 levels).
                const dragged = evt.dragged;
                const targetIsChildList = evt.to.dataset.level === '2';
                return !(targetIsChildList && dragged.querySelector('[data-level="2"] [data-id]'));
            },
        };
        this.$el.querySelectorAll('[data-sortable]').forEach((el) => Sortable.create(el, options));
    },
    serialize() {
        const root = this.$el.querySelector('[data-level="1"]');
        return [...root.children]
            .filter((el) => el.dataset.id)
            .map((el) => ({
                id: Number(el.dataset.id),
                children: [...(el.querySelector('[data-level="2"]')?.children || [])]
                    .filter((child) => child.dataset.id)
                    .map((child) => ({ id: Number(child.dataset.id) })),
            }));
    },
    async save() {
        this.status = 'saving';
        try {
            await postJson(url, { tree: this.serialize() });
            this.status = 'saved';
        } catch (e) {
            this.status = 'error';
        }
    },
}));

/**
 * Section builder: reorder + toggle sections.
 */
Alpine.data('sectionBuilder', (url, sections) => ({
    sections,
    status: 'idle',
    init() {
        Sortable.create(this.$refs.list, {
            handle: '[data-handle]',
            draggable: 'li',
            animation: 150,
            onEnd: () => {
                // Sync the array with the new DOM order (Alpine x-for is keyed by section key).
                const order = [...this.$refs.list.querySelectorAll('li[data-key]')].map((el) => el.dataset.key);
                this.sections = order.map((key) => this.sections.find((s) => s.key === key));
                this.save();
            },
        });
    },
    async save() {
        this.status = 'saving';
        try {
            await postJson(url, { sections: this.sections }, 'PUT');
            this.status = 'saved';
        } catch (e) {
            this.status = 'error';
        }
    },
}));

/**
 * Media library picker used by image fields.
 */
Alpine.data('mediaPicker', (endpoint) => ({
    open: false,
    callback: null,
    loading: false,
    items: [],
    search: '',
    page: 1,
    lastPage: 1,
    async load(page = 1) {
        this.loading = true;
        const params = new URLSearchParams({ images: 1, page, q: this.search });
        const response = await fetch(`${endpoint}?${params}`, { headers: { Accept: 'application/json' } });
        const json = await response.json();
        this.items = page === 1 ? json.data : [...this.items, ...json.data];
        this.page = json.current_page;
        this.lastPage = json.last_page;
        this.loading = false;
    },
    show() {
        this.open = true;
        this.load();
    },
}));

/**
 * Image input with preview, library pick and remove.
 */
Alpine.data('imageField', (initial) => ({
    preview: initial,
    picked: '',
    removed: false,
    onFile(event) {
        const [file] = event.target.files;
        if (file) {
            this.preview = URL.createObjectURL(file);
            this.picked = '';
            this.removed = false;
        }
    },
    pick(item) {
        this.preview = item.url;
        this.picked = item.path;
        this.removed = false;
        this.$refs.file.value = '';
    },
    remove() {
        this.preview = null;
        this.picked = '';
        this.removed = true;
        this.$refs.file.value = '';
    },
}));

/**
 * Live template thumbnail: an iframe rendered at desktop/mobile width and
 * scaled to fit its box. The iframe only loads once it scrolls into view,
 * so a gallery with 50 templates stays fast.
 */
Alpine.data('lazyFrame', (src, width = 1440) => ({
    src: null,
    scale: 0.25,
    loaded: false,
    width,
    init() {
        const fit = () => (this.scale = this.$el.clientWidth / this.width);
        fit();
        new ResizeObserver(fit).observe(this.$el);
        const io = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                this.src = src;
                io.disconnect();
            }
        }, { rootMargin: '300px' });
        io.observe(this.$el);
    },
}));

/**
 * MD5 of a UTF-8 string (hex). Variant rows are posted keyed by md5(label)
 * so option values with dots/brackets stay form-safe (see ProductService).
 */
function md5(input) {
    const str = unescape(encodeURIComponent(input));
    const k = [];
    for (let i = 0; i < 64; i++) k[i] = Math.floor(Math.abs(Math.sin(i + 1)) * 4294967296) | 0;
    const r = [7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22, 7, 12, 17, 22, 5, 9, 14, 20, 5, 9, 14, 20, 5, 9, 14, 20, 5, 9, 14, 20,
        4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23, 4, 11, 16, 23, 6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21, 6, 10, 15, 21];
    const len = str.length;
    const words = new Array((((len + 8) >> 6) + 1) * 16).fill(0);
    for (let i = 0; i < len; i++) words[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
    words[len >> 2] |= 0x80 << ((len % 4) * 8);
    words[words.length - 2] = len * 8;
    let [a0, b0, c0, d0] = [0x67452301, 0xefcdab89, 0x98badcfe, 0x10325476];
    for (let j = 0; j < words.length; j += 16) {
        let [a, b, c, d] = [a0, b0, c0, d0];
        for (let i = 0; i < 64; i++) {
            let f;
            let g;
            if (i < 16) { f = (b & c) | (~b & d); g = i; }
            else if (i < 32) { f = (d & b) | (~d & c); g = (5 * i + 1) % 16; }
            else if (i < 48) { f = b ^ c ^ d; g = (3 * i + 5) % 16; }
            else { f = c ^ (b | ~d); g = (7 * i) % 16; }
            const tmp = d;
            d = c;
            c = b;
            const x = (a + f + k[i] + words[j + g]) | 0;
            b = (b + ((x << r[i]) | (x >>> (32 - r[i])))) | 0;
            a = tmp;
        }
        a0 = (a0 + a) | 0; b0 = (b0 + b) | 0; c0 = (c0 + c) | 0; d0 = (d0 + d) | 0;
    }
    return [a0, b0, c0, d0].map((n) => [0, 8, 16, 24].map((s) => ((n >>> s) & 0xff).toString(16).padStart(2, '0')).join('')).join('');
}

window.md5 = md5;

/**
 * Product options & variants builder (seller dashboard). Options are
 * "name + comma separated values"; every combination becomes a variant row
 * whose label matches ProductService::syncVariants ("M / Black").
 */
Alpine.data('variantBuilder', (config) => ({
    enabled: !!config.enabled,
    options: config.options.length ? config.options : [{ name: '', values: '' }],
    rows: config.variants || {},
    init() {
        this.$watch('options', () => this.regenerate(), { deep: true });
        this.regenerate();
    },
    get combos() {
        const sets = this.options
            .map((o) => ({ name: (o.name || '').trim(), values: [...new Set((o.values || '').split(',').map((v) => v.trim()).filter(Boolean))].slice(0, 30) }))
            .filter((o) => o.name && o.values.length)
            .slice(0, 3);
        if (!sets.length) return [];
        let combos = [[]];
        sets.forEach((set) => {
            combos = combos.flatMap((combo) => set.values.map((v) => [...combo, v]));
        });
        return combos.slice(0, 100).map((c) => c.join(' / '));
    },
    regenerate() {
        this.combos.forEach((label) => this.row(label));
    },
    row(label) {
        if (!this.rows[label]) this.rows[label] = { sku: '', price: '', sale_price: '', stock: '', weight: '', is_active: true };
        return this.rows[label];
    },
    key(label) {
        return md5(label);
    },
    addOption() {
        if (this.options.length < 3) this.options.push({ name: '', values: '' });
    },
    removeOption(index) {
        this.options.splice(index, 1);
        if (!this.options.length) this.options.push({ name: '', values: '' });
    },
    fill(field, value) {
        this.combos.forEach((label) => (this.row(label)[field] = value));
    },
}));

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();
