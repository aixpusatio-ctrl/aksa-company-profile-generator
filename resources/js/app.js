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
            const match = key.match(/^(\w+)\[(\w+)\]$/);
            if (match) {
                data[match[1]] = data[match[1]] || {};
                data[match[1]][match[2]] = value;
            } else {
                data[key] = value;
            }
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

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();
