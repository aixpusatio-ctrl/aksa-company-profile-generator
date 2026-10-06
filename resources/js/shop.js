/**
 * Online shop behaviour for tenant websites (progressive enhancement:
 * every form also works without JavaScript — controllers redirect back
 * with a flash message when the request is not JSON).
 *
 *  - $store.shop         cart count, wishlist ids, cart drawer, toasts
 *  - shopSearch(url)     live search suggestions
 *  - productPage(cfg)    gallery, variant selector, quantity stepper
 *  - checkout(cfg)       multi-step checkout with live shipping quotes
 *
 * Prices shown here always come from the server; the browser only sends
 * ids and quantities.
 */

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

function readConfig() {
    const el = document.getElementById('shop-config');
    if (!el) return null;
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

/** fetch() wrapper: JSON in/out, CSRF header, Laravel-friendly errors. */
async function request(url, { method = 'POST', data = null } = {}) {
    let body = null;
    if (data instanceof FormData) {
        body = data;
    } else if (data) {
        body = new FormData();
        Object.entries(data).forEach(([k, v]) => v !== null && v !== undefined && body.append(k, v));
    }
    if (body && !['GET', 'POST'].includes(method)) {
        body.append('_method', method);
        method = 'POST';
    }

    const res = await fetch(url, {
        method,
        body: method === 'GET' ? null : body,
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() },
        credentials: 'same-origin',
    });

    let json = {};
    try {
        json = await res.json();
    } catch {
        json = {};
    }

    if (!res.ok) {
        const message = res.status === 419
            ? 'Sesi Anda kedaluwarsa. Muat ulang halaman lalu coba lagi.'
            : (json.errors ? Object.values(json.errors).flat()[0] : null) || json.message || 'Terjadi kesalahan. Coba lagi.';
        const error = new Error(message);
        error.status = res.status;
        error.errors = json.errors || {};
        throw error;
    }

    return json;
}

export default function registerShop(Alpine) {
    const config = readConfig();

    Alpine.store('shop', {
        enabled: !!config,
        urls: config?.urls || {},
        drawerMode: !!config?.drawer,
        count: config?.count || 0,
        wishlist: (config?.wishlist || []).map(Number),
        drawer: false,
        cart: null,
        loadingCart: false,
        busy: false,
        toasts: [],

        // ------------------------------------------------------------ Helpers
        inWishlist(id) {
            return this.wishlist.includes(Number(id));
        },

        setCount(count) {
            this.count = Number(count) || 0;
            document.querySelectorAll('[data-cart-count]').forEach((el) => {
                el.textContent = this.count;
                el.classList.toggle('hidden', this.count === 0);
                el.classList.toggle('inline-flex', this.count > 0);
            });
        },

        syncWishlistCount() {
            document.querySelectorAll('[data-wishlist-count]').forEach((el) => {
                el.textContent = this.wishlist.length;
                el.classList.toggle('hidden', this.wishlist.length === 0);
                el.classList.toggle('inline-flex', this.wishlist.length > 0);
            });
        },

        toast(message, type = 'success', link = null) {
            if (!message) return;
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, link });
            setTimeout(() => this.dismiss(id), type === 'error' ? 6000 : 4000);
        },

        dismiss(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },

        applyCart(data) {
            if (!data) return;
            if (data.count !== undefined) this.setCount(data.count);
            if (data.items) this.cart = data;
        },

        // ------------------------------------------------------------ Cart
        /** Add to cart from a <form> (product_id, variant_id, quantity, buy_now). */
        async add(form, submitter = null) {
            if (this.busy) return;
            const data = new FormData(form);
            if (submitter?.name) data.set(submitter.name, submitter.value);
            this.busy = true;
            try {
                const json = await request(form.action, { data });
                if (json.redirect) {
                    window.location.href = json.redirect;
                    return;
                }
                this.applyCart(json);
                if (this.drawerMode) {
                    this.drawer = true;
                    this.toast(json.message);
                } else {
                    this.toast(json.message, 'success', { href: this.urls.cart, label: 'Lihat keranjang' });
                }
            } catch (e) {
                if (!e.status) {
                    form.submit(); // network/JS problem: fall back to a normal POST
                    return;
                }
                this.toast(e.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        async loadCart() {
            if (!this.urls.summary) return;
            this.loadingCart = true;
            try {
                this.applyCart(await request(this.urls.summary, { method: 'GET' }));
            } catch (e) {
                this.toast(e.message, 'error');
            } finally {
                this.loadingCart = false;
            }
        },

        openDrawer() {
            this.drawer = true;
            this.loadCart();
        },

        async updateItem(id, quantity) {
            try {
                this.applyCart(await request(`${this.urls.cart}/${id}`, { method: 'PATCH', data: { quantity } }));
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async removeItem(id) {
            try {
                const json = await request(`${this.urls.cart}/${id}`, { method: 'DELETE', data: {} });
                this.applyCart(json);
                this.toast(json.message);
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        // ------------------------------------------------------------ Wishlist
        async toggleWishlist(id, form = null) {
            id = Number(id);
            const was = this.inWishlist(id);
            this.wishlist = was ? this.wishlist.filter((x) => x !== id) : [...this.wishlist, id];
            this.syncWishlistCount();
            try {
                const json = await request(form?.action || `${this.urls.wishlist}/${id}`, { data: new FormData(form || undefined) });
                const now = !!json.in_wishlist;
                if (now !== !was) {
                    this.wishlist = now ? [...new Set([...this.wishlist, id])] : this.wishlist.filter((x) => x !== id);
                    this.syncWishlistCount();
                }
                this.toast(json.message, 'success', now ? { href: this.urls.wishlistPage, label: 'Lihat wishlist' } : null);
            } catch (e) {
                this.wishlist = was ? [...this.wishlist, id] : this.wishlist.filter((x) => x !== id);
                this.syncWishlistCount();
                this.toast(e.message, 'error');
            }
        },
    });

    // ---------------------------------------------------------------- Search
    Alpine.data('shopSearch', (url) => ({
        q: '',
        results: [],
        allUrl: '#',
        open: false,
        loading: false,
        controller: null,
        init() {
            this.q = this.$el.querySelector('input[name="q"]')?.value || '';
        },
        async search() {
            const term = this.q.trim();
            if (term.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.controller?.abort();
            this.controller = new AbortController();
            this.loading = true;
            try {
                const res = await fetch(`${url}?q=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' }, signal: this.controller.signal });
                const json = await res.json();
                this.results = json.results || [];
                this.allUrl = json.all_url;
                this.open = true;
            } catch {
                /* aborted or offline: keep the plain form */
            } finally {
                this.loading = false;
            }
        },
    }));

    // ---------------------------------------------------------------- Product page
    Alpine.data('productPage', (cfg) => ({
        images: cfg.images || [],
        active: 0,
        variants: cfg.variants || [],
        options: cfg.options || [], // [{id, name, values: [{id, value}]}]
        selected: {}, // optionId => valueId
        qty: 1,
        basePrice: cfg.price,
        baseOriginal: cfg.original,
        baseSku: cfg.sku,
        baseStock: cfg.stock, // null = unlimited
        lightbox: false,

        init() {
            // Preselect when every option has a single value.
            this.options.forEach((o) => {
                if (o.values.length === 1) this.selected[o.id] = o.values[0].id;
            });
        },

        get hasVariants() {
            return this.variants.length > 0;
        },
        get complete() {
            return this.options.every((o) => this.selected[o.id]);
        },
        get variant() {
            if (!this.hasVariants || !this.complete) return null;
            const ids = Object.values(this.selected).map(Number).sort();
            return this.variants.find((v) => [...v.values].map(Number).sort().join(',') === ids.join(',')) || null;
        },
        get price() {
            return this.variant ? this.variant.price_formatted : this.basePrice;
        },
        get original() {
            return this.variant ? this.variant.original : this.baseOriginal;
        },
        get sku() {
            return this.variant?.sku || this.baseSku;
        },
        get stock() {
            if (this.hasVariants) return this.variant ? this.variant.available : null;
            return this.baseStock;
        },
        get maxQty() {
            const s = this.stock;
            return Math.max(1, Math.min(99, s === null || s === undefined ? 99 : s));
        },
        get canBuy() {
            if (this.hasVariants) return !!this.variant && this.variant.available > 0;
            return cfg.inStock;
        },
        get stockLabel() {
            if (this.hasVariants && !this.complete) return 'Pilih varian';
            if (this.hasVariants && !this.variant) return 'Kombinasi tidak tersedia';
            if (!this.canBuy) return 'Stok habis';
            const s = this.stock;
            if (cfg.showStock && s !== null && s < 999 && s <= cfg.lowStock) return `Sisa ${s} — segera habis`;
            if (cfg.showStock && s !== null && s < 999) return `Stok tersedia (${s})`;
            return 'Tersedia';
        },

        /** Whether choosing a value keeps at least one purchasable variant. */
        valueAvailable(optionId, valueId) {
            if (!this.hasVariants) return true;
            const others = Object.entries(this.selected).filter(([o]) => Number(o) !== Number(optionId)).map(([, v]) => Number(v));
            return this.variants.some((v) => {
                const vals = v.values.map(Number);
                return vals.includes(Number(valueId)) && others.every((x) => vals.includes(x)) && v.available > 0;
            });
        },
        choose(optionId, valueId) {
            this.selected[optionId] = this.selected[optionId] === valueId ? undefined : valueId;
            if (this.selected[optionId] === undefined) delete this.selected[optionId];
            this.qty = Math.min(this.qty, this.maxQty);
            const image = this.variant?.image;
            if (image) {
                let i = this.images.indexOf(image);
                if (i < 0) {
                    this.images = [image, ...this.images];
                    i = 0;
                }
                this.active = i;
            }
        },
        inc() {
            this.qty = Math.min(this.maxQty, this.qty + 1);
        },
        dec() {
            this.qty = Math.max(1, this.qty - 1);
        },
        submit(event) {
            if (this.hasVariants && !this.variant) {
                this.$store.shop?.toast('Pilih varian produk terlebih dahulu.', 'error');
                return;
            }
            if (this.$store.shop?.enabled) this.$store.shop.add(event.target, event.submitter);
            else event.target.submit();
        },
        whatsappHref(base, name) {
            const lines = [`Halo, saya tertarik dengan produk ${name}`];
            if (this.variant) lines.push(`Varian: ${this.variant.label}`);
            lines.push(`Jumlah: ${this.qty}`, window.location.href);
            return `${base}?text=${encodeURIComponent(lines.join('\n'))}`;
        },
    }));

    // ---------------------------------------------------------------- Checkout
    Alpine.data('checkout', (cfg) => ({
        step: 1,
        steps: ['Kontak', 'Pengiriman', 'Pembayaran', 'Konfirmasi'],
        shippingMethods: cfg.shippingMethods || [],
        shippingId: cfg.shippingId ? Number(cfg.shippingId) : null,
        paymentId: cfg.paymentId ? Number(cfg.paymentId) : null,
        totals: cfg.totals,
        coupon: cfg.coupon || '',
        couponError: null,
        quoting: false,
        addressId: null,
        enhanced: false,

        init() {
            this.enhanced = true;
            if (cfg.startStep) this.step = cfg.startStep;
            this.quote();
        },

        get selectedShipping() {
            return this.shippingMethods.find((m) => Number(m.methodId) === this.shippingId) || null;
        },
        get needsAddress() {
            return !this.selectedShipping || this.selectedShipping.requiresAddress;
        },

        field(name) {
            return this.$root.querySelector(`[name="${name}"]`);
        },

        useAddress(address) {
            this.addressId = address.id;
            Object.entries({ name: address.name, phone: address.phone, address: address.address, city: address.city, province: address.province, postal_code: address.postal_code, country: address.country })
                .forEach(([k, v]) => {
                    const el = this.field(`address[${k}]`);
                    if (el) el.value = v || '';
                });
            this.quote();
        },

        async quote() {
            this.quoting = true;
            try {
                const json = await request(cfg.quoteUrl, {
                    data: {
                        city: this.field('address[city]')?.value || '',
                        province: this.field('address[province]')?.value || '',
                        postal_code: this.field('address[postal_code]')?.value || '',
                        shipping_method_id: this.shippingId || '',
                        coupon: this.coupon.trim(),
                    },
                });
                this.shippingMethods = json.shipping_methods || [];
                if (this.shippingId && !this.selectedShipping) this.shippingId = null;
                this.totals = json.totals;
                this.couponError = json.totals.coupon_error;
            } catch (e) {
                this.$store.shop?.toast(e.message, 'error');
            } finally {
                this.quoting = false;
            }
        },

        chooseShipping(id) {
            this.shippingId = Number(id);
            this.quote();
        },

        /** Validate the inputs of the current step with the browser's constraint API. */
        validStep(n) {
            const panel = this.$root.querySelector(`[data-step="${n}"]`);
            if (!panel) return true;
            const fields = [...panel.querySelectorAll('input, select, textarea')].filter((el) => !el.disabled && el.offsetParent !== null);
            for (const el of fields) {
                if (!el.checkValidity()) {
                    el.reportValidity();
                    return false;
                }
            }
            if (n === 2 && !this.shippingId && this.shippingMethods.length) {
                this.$store.shop?.toast('Pilih metode pengiriman.', 'error');
                return false;
            }
            return true;
        },

        next() {
            if (!this.validStep(this.step)) return;
            this.step = Math.min(this.steps.length, this.step + 1);
            if (this.step === 2) this.quote();
            this.$nextTick(() => this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' }));
        },
        prev() {
            this.step = Math.max(1, this.step - 1);
        },
        goto(n) {
            if (n < this.step) this.step = n;
        },
    }));
}
