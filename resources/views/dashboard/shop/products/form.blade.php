@php
    use App\Models\Shop\Product;
    use App\Support\Shop\Money;

    $editing = $product->exists;
    $dt = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : null;
    $currency = trim(Money::CURRENCIES[Money::currency()][0] ?? Money::currency());

    // Options & variants (old input wins after a validation error).
    $initialOptions = collect(old('options', $editing ? $product->options->map(fn ($o) => ['name' => $o->name, 'values' => $o->values->pluck('value')->implode(', ')])->all() : []))
        ->map(fn ($o) => ['name' => (string) ($o['name'] ?? ''), 'values' => (string) ($o['values'] ?? '')])->values()->all();

    $initialVariants = [];
    if ($editing) {
        foreach ($product->variants as $variant) {
            $initialVariants[$variant->label] = [
                'sku' => (string) $variant->sku, 'price' => $variant->price !== null ? (float) $variant->price : '',
                'sale_price' => $variant->sale_price !== null ? (float) $variant->sale_price : '', 'stock' => (int) $variant->stock,
                'weight' => $variant->weight ?? '', 'is_active' => (bool) $variant->is_active,
            ];
        }
    }
    if (is_array(old('variants'))) {
        // Rebuild labels from the submitted options to map md5 keys back to rows.
        $combos = [[]];
        foreach ($initialOptions as $option) {
            $values = collect(explode(',', $option['values']))->map(fn ($v) => trim($v))->filter()->unique()->values();
            if (trim($option['name']) === '' || $values->isEmpty()) {
                continue;
            }
            $combos = collect($combos)->flatMap(fn ($c) => $values->map(fn ($v) => [...$c, $v]))->all();
        }
        foreach ($combos as $combo) {
            $label = implode(' / ', $combo);
            if ($label !== '' && $row = old('variants.'.md5($label))) {
                $initialVariants[$label] = ['sku' => $row['sku'] ?? '', 'price' => $row['price'] ?? '', 'sale_price' => $row['sale_price'] ?? '', 'stock' => $row['stock'] ?? '', 'weight' => $row['weight'] ?? '', 'is_active' => (bool) ($row['is_active'] ?? false)];
            }
        }
    }
    $hasVariants = (bool) old('has_variants', $editing && $product->variants->isNotEmpty());

    $specs = collect(old('specifications', $product->specifications ?? []))->map(fn ($s) => ['label' => (string) ($s['label'] ?? ''), 'value' => (string) ($s['value'] ?? '')])->values()->all();
    $selectedTags = collect(old('tags', $editing ? $product->tags->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $relatedIds = collect(old('related_ids', $product->related_ids ?? []))->map(fn ($id) => (int) $id)->all();
    $ctaValue = fn ($key) => old('cta.'.$key, array_key_exists($key, $product->cta ?? []) ? (($product->cta[$key] ?? false) ? '1' : '0') : 'default');
    $ctaLabels = ['add_to_cart' => 'Tambah ke keranjang', 'buy_now' => 'Beli sekarang', 'whatsapp' => 'Pesan via WhatsApp', 'contact' => 'Hubungi kami'];
    $settings = $company->shopSetting;
    $tagColors = [
        'slate' => 'bg-slate-100 text-slate-700', 'sky' => 'bg-sky-100 text-sky-700', 'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700', 'violet' => 'bg-violet-100 text-violet-700', 'emerald' => 'bg-emerald-100 text-emerald-700',
    ];
@endphp
<x-website-layout :company="$company" :title="$editing ? 'Edit Produk' : 'Tambah Produk'">
    @include('dashboard.shop.partials.nav')

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="min-w-0">
            <a href="{{ route('websites.shop.products.index', $company) }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="size-4" /> Semua produk</a>
            <h2 class="mt-1 truncate text-xl font-bold text-slate-900">{{ $editing ? $product->name : 'Produk baru' }}</h2>
        </div>
        @if ($editing)
            <div class="flex flex-wrap gap-2">
                @if ($product->isPublished() && $company->hasShop())
                    <a href="{{ $company->publicUrl() }}/shop/product/{{ $product->slug }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><x-icon name="external" class="size-3.5" /> Lihat</a>
                @endif
                <form method="POST" action="{{ route('websites.shop.products.duplicate', [$company, $product]) }}">@csrf<button class="btn btn-secondary btn-sm"><x-icon name="duplicate" class="size-3.5" /> Duplikat</button></form>
                <x-confirm-delete :action="route('websites.shop.products.destroy', [$company, $product])" message="Hapus produk ini? Produk yang pernah dipesan akan diarsipkan." label="Hapus" />
            </div>
        @endif
    </div>

    <form method="POST" action="{{ $editing ? route('websites.shop.products.update', [$company, $product]) : route('websites.shop.products.store', $company) }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="min-w-0 space-y-6 xl:col-span-2">
            {{-- Basic info --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Informasi dasar</h3></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <x-form.input name="name" label="Nama produk" :value="$product->name" required maxlength="200" class="sm:col-span-2" />
                    <x-form.input name="slug" label="Slug URL" :value="$product->slug" maxlength="120" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" help="Kosongkan untuk dibuat otomatis dari nama." />
                    <x-form.input name="brand" label="Brand" :value="$product->brand" maxlength="100" />
                    <x-form.textarea name="short_description" label="Deskripsi singkat" :value="$product->short_description" rows="2" maxlength="500" class="sm:col-span-2" help="Tampil di kartu produk & bagian atas halaman produk." />
                    <x-form.rich name="description" label="Deskripsi lengkap" :value="$product->description" class="sm:col-span-2" />
                </div>
            </div>

            {{-- Images --}}
            <div class="card" x-data="{ previews: [], onFiles(e) { this.previews = [...e.target.files].slice(0, 10).map((f) => URL.createObjectURL(f)); } }">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Foto produk</h3>
                    <p class="text-xs text-slate-500">Foto pertama menjadi foto utama. Seret untuk mengubah urutan (maks 10 per unggahan).</p>
                </div>
                <div class="card-body space-y-4">
                    @if ($editing && $product->images->isNotEmpty())
                        <div x-data="sortableList(@js(route('websites.shop.products.images.reorder', [$company, $product])))">
                            <ul x-ref="list" class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                                @foreach ($product->images as $image)
                                    <li data-id="{{ $image->id }}" class="group relative aspect-square overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
                                        <img src="{{ $image->url('image') }}" alt="{{ $image->alt }}" class="size-full object-cover">
                                        <button type="button" data-handle class="absolute inset-0 cursor-grab" aria-label="Seret untuk mengurutkan"></button>
                                        @if ($loop->first)<span class="absolute bottom-1 left-1 rounded bg-slate-900/70 px-1.5 py-0.5 text-[10px] font-semibold text-white">Utama</span>@endif
                                        <button type="button" class="absolute top-1 right-1 rounded-lg bg-white/90 p-1 text-rose-600 shadow hover:bg-white" title="Hapus foto"
                                                @click="if (confirm('Hapus foto ini?')) postJson(@js(route('websites.shop.products.images.destroy', [$company, $product, $image])), {}, 'DELETE').then(() => $el.closest('li').remove())">
                                            <x-icon name="trash" class="size-4" />
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <p x-cloak x-show="saving" class="mt-2 text-xs text-slate-500">Menyimpan urutan…</p>
                        </div>
                    @endif
                    <label class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 px-4 py-6 text-center hover:border-brand-300 hover:bg-brand-50/30">
                        <x-icon name="upload" class="size-6 text-slate-400" />
                        <span class="mt-2 text-sm font-medium text-slate-700">Pilih foto (bisa lebih dari satu)</span>
                        <span class="text-xs text-slate-500">JPG, PNG, WEBP atau GIF</span>
                        <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp,image/gif" multiple class="sr-only" @change="onFiles">
                    </label>
                    <div x-cloak x-show="previews.length" class="grid grid-cols-4 gap-2 sm:grid-cols-6">
                        <template x-for="src in previews" :key="src"><img :src="src" alt="" class="aspect-square w-full rounded-lg object-cover ring-1 ring-slate-200"></template>
                    </div>
                    @error('images')<p class="form-error">{{ $message }}</p>@enderror
                    @error('images.*')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Pricing --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Harga</h3>
                    <p class="text-xs text-slate-500">Harga promo berlaku sesuai jadwal (kosongkan jadwal untuk berlaku langsung).</p>
                </div>
                <div class="card-body grid gap-5 sm:grid-cols-3">
                    <x-form.input name="price" type="number" step="any" min="0" label="Harga normal" :value="$product->price !== null ? (float) $product->price : null" :prefix="$currency" required />
                    <x-form.input name="compare_price" type="number" step="any" min="0" label="Harga coret" :value="$product->compare_price !== null ? (float) $product->compare_price : null" :prefix="$currency" help="Harga sebelum diskon (opsional)." />
                    <x-form.input name="cost_price" type="number" step="any" min="0" label="Harga modal" :value="$product->cost_price !== null ? (float) $product->cost_price : null" :prefix="$currency" help="Tidak tampil ke pelanggan." />
                    <x-form.input name="sale_price" type="number" step="any" min="0" label="Harga promo" :value="$product->sale_price !== null ? (float) $product->sale_price : null" :prefix="$currency" />
                    <x-form.input name="sale_starts_at" type="datetime-local" label="Promo mulai" :value="$dt($product->sale_starts_at)" />
                    <x-form.input name="sale_ends_at" type="datetime-local" label="Promo berakhir" :value="$dt($product->sale_ends_at)" />
                </div>
            </div>

            {{-- Inventory & shipping --}}
            <div class="card" x-data="{ track: @js((bool) old('track_stock', $product->track_stock ?? true)) }">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Inventori & pengiriman</h3></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <x-form.input name="sku" label="SKU" :value="$product->sku" maxlength="64" />
                    <x-form.select name="stock_status" label="Status stok" :options="Product::STOCK_STATUSES" :value="$product->stock_status ?? 'in_stock'" />
                    <label class="flex items-start gap-3 sm:col-span-2">
                        <input type="hidden" name="track_stock" value="0">
                        <input type="checkbox" name="track_stock" value="1" class="form-checkbox mt-0.5" x-model="track">
                        <span><span class="block text-sm font-medium text-slate-800">Lacak stok</span><span class="block text-xs text-slate-500">Stok berkurang otomatis saat ada pesanan; produk tidak bisa dibeli bila habis (kecuali backorder).</span></span>
                    </label>
                    <div x-show="track" class="sm:col-span-2 grid gap-5 sm:grid-cols-2">
                        @if ($editing)
                            <div>
                                <p class="form-label">Stok saat ini</p>
                                <p class="text-sm text-slate-700">
                                    <span class="font-semibold">{{ $product->hasVariants() ? $product->variants->sum('stock') : $product->stock }}</span> unit
                                    @if ($product->reserved_stock) · {{ $product->reserved_stock }} dipesan @endif
                                </p>
                                <a href="{{ route('websites.shop.inventory.index', [$company, 'q' => $product->sku ?: $product->name]) }}" class="text-xs font-medium text-brand-600 hover:underline">Sesuaikan stok di Inventori →</a>
                            </div>
                        @else
                            <x-form.input name="stock" type="number" min="0" label="Stok awal" :value="0" help="Untuk produk bervarian, isi stok per varian di bawah." />
                        @endif
                        <x-form.input name="low_stock_threshold" type="number" min="0" label="Batas stok menipis" :value="$product->low_stock_threshold" :placeholder="'Default toko: '.($settings?->low_stock_threshold ?? 5)" />
                    </div>
                    <x-form.input name="weight" type="number" min="0" label="Berat" :value="$product->weight" suffix="gram" help="Dipakai untuk ongkir per kg." />
                </div>
            </div>

            {{-- Options & variants --}}
            <div class="card" x-data="variantBuilder(@js(['enabled' => $hasVariants, 'options' => $initialOptions, 'variants' => (object) $initialVariants]))">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Opsi & varian</h3>
                        <p class="text-xs text-slate-500">Mis. Ukuran: S, M, L dan Warna: Hitam, Putih → 6 varian.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="hidden" name="has_variants" value="0">
                        <input type="checkbox" name="has_variants" value="1" class="form-checkbox" x-model="enabled"> Produk memiliki varian
                    </label>
                </div>
                <div x-show="enabled" x-collapse>
                    <div class="card-body space-y-4">
                        <template x-for="(option, index) in options" :key="index">
                            <div class="grid gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-[180px_1fr_auto] sm:items-end">
                                <div>
                                    <label class="form-label">Nama opsi</label>
                                    <input type="text" class="form-input" :name="enabled ? `options[${index}][name]` : null" x-model.debounce.400ms="option.name" maxlength="60" placeholder="Ukuran">
                                </div>
                                <div>
                                    <label class="form-label">Nilai (pisahkan dengan koma)</label>
                                    <input type="text" class="form-input" :name="enabled ? `options[${index}][values]` : null" x-model.debounce.400ms="option.values" maxlength="500" placeholder="S, M, L, XL">
                                </div>
                                <button type="button" class="btn btn-ghost text-rose-600" @click="removeOption(index)" title="Hapus opsi"><x-icon name="trash" class="size-4" /></button>
                            </div>
                        </template>
                        <button type="button" class="btn btn-secondary btn-sm" @click="addOption()" x-show="options.length < 3"><x-icon name="plus" class="size-3.5" /> Tambah opsi</button>

                        <template x-if="combos.length">
                            <div>
                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-800"><span x-text="combos.length"></span> varian</p>
                                    <div class="flex flex-wrap gap-2 text-xs" x-data="{ bulkPrice: '', bulkStock: '' }">
                                        <input type="number" min="0" step="any" x-model="bulkPrice" placeholder="Harga semua" class="form-input w-32 py-1 text-xs">
                                        <button type="button" class="btn btn-secondary btn-sm" @click="fill('price', bulkPrice)">Terapkan</button>
                                        <input type="number" min="0" x-model="bulkStock" placeholder="Stok semua" class="form-input w-28 py-1 text-xs">
                                        <button type="button" class="btn btn-secondary btn-sm" @click="fill('stock', bulkStock)">Terapkan</button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto rounded-xl ring-1 ring-slate-200">
                                    <table class="table">
                                        <thead><tr><th>Varian</th><th>SKU</th><th>Harga</th><th>Harga promo</th><th>Stok</th><th>Berat (g)</th><th>Aktif</th></tr></thead>
                                        <tbody class="divide-y divide-slate-100">
                                            <template x-for="label in combos" :key="label">
                                                <tr>
                                                    <td class="font-medium whitespace-nowrap text-slate-900" x-text="label"></td>
                                                    <td><input type="text" class="form-input w-32 py-1.5 text-xs" :name="`variants[${key(label)}][sku]`" x-model="row(label).sku" maxlength="64" placeholder="Otomatis"></td>
                                                    <td><input type="number" step="any" min="0" class="form-input w-28 py-1.5 text-xs" :name="`variants[${key(label)}][price]`" x-model="row(label).price" placeholder="Harga produk"></td>
                                                    <td><input type="number" step="any" min="0" class="form-input w-28 py-1.5 text-xs" :name="`variants[${key(label)}][sale_price]`" x-model="row(label).sale_price"></td>
                                                    <td><input type="number" min="0" class="form-input w-20 py-1.5 text-xs" :name="`variants[${key(label)}][stock]`" x-model="row(label).stock"></td>
                                                    <td><input type="number" min="0" class="form-input w-20 py-1.5 text-xs" :name="`variants[${key(label)}][weight]`" x-model="row(label).weight"></td>
                                                    <td>
                                                        <input type="hidden" :name="`variants[${key(label)}][is_active]`" value="0">
                                                        <input type="checkbox" class="form-checkbox" :name="`variants[${key(label)}][is_active]`" value="1" x-model="row(label).is_active">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="form-help">Harga kosong = mengikuti harga produk. Mengubah stok varian tercatat di riwayat inventori.</p>
                            </div>
                        </template>
                        @error('options')<p class="form-error">{{ $message }}</p>@enderror
                        @error('variants')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="card" x-data="{ rows: @js($specs ?: [['label' => '', 'value' => '']]) }">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Spesifikasi</h3>
                    <p class="text-xs text-slate-500">Tabel spesifikasi di halaman produk, mis. Material: Kayu jati.</p>
                </div>
                <div class="card-body space-y-2">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex gap-2">
                            <input type="text" class="form-input w-2/5" :name="`specifications[${index}][label]`" x-model="row.label" maxlength="60" placeholder="Label">
                            <input type="text" class="form-input flex-1" :name="`specifications[${index}][value]`" x-model="row.value" maxlength="250" placeholder="Nilai">
                            <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600" @click="rows.splice(index, 1)" title="Hapus"><x-icon name="x" class="size-4" /></button>
                        </div>
                    </template>
                    <button type="button" class="btn btn-secondary btn-sm" @click="rows.push({ label: '', value: '' })" x-show="rows.length < 30"><x-icon name="plus" class="size-3.5" /> Tambah baris</button>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">SEO</h3></div>
                <div class="card-body grid gap-5">
                    <x-form.input name="seo_title" label="Meta title" :value="$product->seo_title" maxlength="120" help="Kosongkan untuk memakai nama produk." />
                    <x-form.textarea name="seo_description" label="Meta description" :value="$product->seo_description" rows="2" maxlength="500" />
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="min-w-0 space-y-6">
            <div class="card">
                <div class="card-body space-y-4">
                    <x-form.select name="status" label="Status" :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']" :value="$product->status" />
                    <x-form.input name="published_at" type="datetime-local" label="Jadwal tayang" :value="$dt($product->published_at)" help="Kosong = tayang saat dipublikasikan." />
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" name="featured" value="1" class="form-checkbox" @checked(old('featured', $product->featured))>
                        <span class="text-sm font-medium text-slate-800">Produk unggulan (featured)</span>
                    </label>
                    <button class="btn btn-primary w-full"><x-icon name="check" class="size-4" /> {{ $editing ? 'Simpan perubahan' : 'Simpan produk' }}</button>
                </div>
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Kategori</h3>
                    <a href="{{ route('websites.shop.categories.index', $company) }}" class="text-xs font-medium text-brand-600 hover:underline">Kelola</a>
                </div>
                <div class="card-body">
                    <select name="category_id" class="form-input">
                        <option value="">Tanpa kategori</option>
                        @foreach ($categories->whereNull('parent_id') as $root)
                            <option value="{{ $root->id }}" @selected((int) old('category_id', $product->category_id) === $root->id)>{{ $root->name }}</option>
                            @foreach ($categories->where('parent_id', $root->id) as $child)
                                <option value="{{ $child->id }}" @selected((int) old('category_id', $product->category_id) === $child->id)>— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card" x-data="{
                tags: @js($tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'color' => $t->color])->values()),
                selected: @js($selectedTags),
                colors: @js($tagColors),
                name: '', color: 'slate', error: '', busy: false,
                async add() {
                    if (!this.name.trim()) return;
                    this.busy = true; this.error = '';
                    try {
                        const tag = await postJson(@js(route('websites.shop.tags.store', $company)), { name: this.name.trim(), color: this.color });
                        if (!this.tags.find((t) => t.id === tag.id)) this.tags.push(tag);
                        if (!this.selected.includes(tag.id)) this.selected.push(tag.id);
                        this.name = '';
                    } catch (e) { this.error = 'Gagal menambah tag.'; }
                    this.busy = false;
                },
            }">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Tag</h3></div>
                <div class="card-body space-y-3">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in tags" :key="tag.id">
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" :value="tag.id" x-model.number="selected" class="peer sr-only">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold transition peer-focus-visible:outline-2 peer-focus-visible:outline-brand-500" :class="selected.includes(tag.id) ? colors[tag.color] + ' ring-2 ring-brand-500' : 'bg-white text-slate-500 ring-1 ring-slate-200'" x-text="tag.name"></span>
                            </label>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" x-model="name" maxlength="60" placeholder="Tag baru…" class="form-input py-1.5 text-sm" @keydown.enter.prevent="add()">
                        <select x-model="color" class="form-input w-28 py-1.5 text-sm">
                            @foreach (\App\Models\Shop\ProductTag::COLORS as $color)<option value="{{ $color }}">{{ ucfirst($color) }}</option>@endforeach
                        </select>
                        <button type="button" class="btn btn-secondary btn-sm" @click="add()" :disabled="busy"><x-icon name="plus" class="size-3.5" /></button>
                    </div>
                    <p x-cloak x-show="error" class="form-error" x-text="error"></p>
                </div>
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Tombol aksi (CTA)</h3>
                    <p class="text-xs text-slate-500">"Default" mengikuti pengaturan toko.</p>
                </div>
                <div class="card-body space-y-3">
                    @foreach ($ctaLabels as $key => $label)
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm text-slate-700">{{ $label }}</span>
                            <select name="cta[{{ $key }}]" class="form-input w-36 py-1.5 text-sm">
                                <option value="default" @selected($ctaValue($key) === 'default')>Default ({{ $settings?->option('cta_'.$key) ? 'on' : 'off' }})</option>
                                <option value="1" @selected($ctaValue($key) === '1')>Tampilkan</option>
                                <option value="0" @selected($ctaValue($key) === '0')>Sembunyikan</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card" x-data="{ q: '', selected: @js($relatedIds) }">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Produk terkait</h3>
                    <p class="text-xs text-slate-500">Maks 12. Kosong = otomatis dari kategori yang sama.</p>
                </div>
                <div class="card-body space-y-2">
                    @if ($allProducts->isEmpty())
                        <p class="text-sm text-slate-500">Belum ada produk lain.</p>
                    @else
                        <input type="search" x-model="q" placeholder="Cari produk…" class="form-input py-1.5 text-sm">
                        <div class="max-h-56 space-y-1 overflow-y-auto pr-1">
                            @foreach ($allProducts as $other)
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1 text-sm hover:bg-slate-50" x-show="!q || @js(mb_strtolower($other->name)).includes(q.toLowerCase())">
                                    <input type="checkbox" name="related_ids[]" value="{{ $other->id }}" class="form-checkbox" x-model.number="selected" :disabled="selected.length >= 12 && !selected.includes({{ $other->id }})">
                                    <span class="truncate">{{ $other->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-400"><span x-text="selected.length"></span>/12 dipilih</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="xl:col-span-3 flex justify-end gap-2 border-t border-slate-200 pt-4">
            <a href="{{ route('websites.shop.products.index', $company) }}" class="btn btn-secondary">Batal</a>
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Simpan perubahan' : 'Simpan produk' }}</button>
        </div>
    </form>
</x-website-layout>
