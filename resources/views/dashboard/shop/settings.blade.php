@php
    $options = $settings->options();
    $toggleGroups = [
        'Tombol aksi produk (CTA)' => [
            'cta_add_to_cart' => ['Tambah ke keranjang', 'Tombol "Add to cart" di kartu & halaman produk.'],
            'cta_buy_now' => ['Beli sekarang', 'Langsung ke checkout dengan produk ini.'],
            'cta_whatsapp' => ['Pesan via WhatsApp', 'Membuka chat WhatsApp berisi detail produk.'],
            'cta_contact' => ['Hubungi kami', 'Arahkan ke halaman/section kontak (cocok untuk produk B2B).'],
        ],
        'Pesanan & checkout' => [
            'whatsapp_checkout' => ['Checkout via WhatsApp', 'Pelanggan dapat mengirim pesanan lewat WhatsApp.'],
            'guest_checkout' => ['Checkout tanpa akun', 'Pelanggan boleh checkout tanpa mendaftar.'],
            'require_phone' => ['Wajib nomor telepon', 'Nomor telepon wajib diisi saat checkout.'],
            'order_note' => ['Catatan pesanan', 'Tampilkan kolom catatan di checkout.'],
        ],
        'Inventori' => [
            'allow_backorder' => ['Izinkan backorder', 'Pesanan tetap bisa dibuat saat stok habis.'],
            'show_stock' => ['Tampilkan jumlah stok', 'Tampilkan sisa stok di halaman produk.'],
        ],
        'Ulasan' => [
            'reviews_enabled' => ['Aktifkan ulasan', 'Pelanggan dapat memberi rating & ulasan.'],
            'reviews_moderation' => ['Moderasi ulasan', 'Ulasan baru tampil setelah Anda setujui.'],
            'reviews_require_purchase' => ['Hanya pembeli', 'Hanya pelanggan yang pernah membeli yang dapat mengulas.'],
        ],
    ];
    $bool = fn ($key) => (bool) old('options.'.$key, $errors->any() ? false : $options[$key]);
@endphp
<x-website-layout :company="$company" title="Pengaturan Toko">
    @include('dashboard.shop.partials.nav')

    <div class="grid gap-6 xl:grid-cols-3">
        <form method="POST" action="{{ route('websites.shop.settings.update', $company) }}" enctype="multipart/form-data" class="space-y-6 xl:col-span-2">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Informasi toko</h2>
                    <p class="text-xs text-slate-500">Nama & mata uang yang dipakai di katalog, checkout dan invoice.</p>
                </div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <x-form.input name="name" label="Nama toko" :value="$settings->name" :placeholder="$company->name" maxlength="120" class="sm:col-span-2" />
                    <x-form.textarea name="description" label="Deskripsi toko" :value="$settings->description" rows="3" maxlength="1000" class="sm:col-span-2" />
                    <x-form.select name="currency" label="Mata uang" :options="array_combine($currencies, $currencies)" :value="$settings->currency" />
                    <x-form.input name="order_prefix" label="Prefix nomor pesanan" :value="$settings->order_prefix" maxlength="10" required help="Huruf/angka saja, contoh: INV → INV-2026-0001." />
                    <x-form.input name="low_stock_threshold" type="number" min="0" label="Batas stok menipis" :value="$settings->low_stock_threshold" required help="Default untuk produk tanpa batas khusus." />
                    <x-form.input name="options[min_order]" type="number" min="0" step="any" label="Minimum belanja" :value="$options['min_order']" help="Kosongkan bila tidak ada minimum." />
                    <div class="rounded-xl bg-slate-50 p-4 text-sm sm:col-span-2">
                        <p class="flex items-center gap-2 font-medium text-slate-800"><x-icon name="whatsapp" class="size-4 text-emerald-600" /> WhatsApp toko</p>
                        <p class="mt-1 text-slate-600">
                            @if ($company->whatsapp)
                                Pesanan & tombol WhatsApp dikirim ke <span class="font-semibold">{{ $company->whatsapp }}</span>.
                            @else
                                <span class="text-amber-700">Nomor WhatsApp belum diisi</span> — tombol & checkout WhatsApp tidak akan tampil.
                            @endif
                            <a href="{{ route('websites.edit', [$company, 'contact']) }}" class="font-medium text-brand-600 hover:underline">Ubah di Contact →</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Halaman Shop (hero & banner)</h2>
                    <p class="text-xs text-slate-500">Judul, subjudul dan gambar banner di halaman /shop.</p>
                </div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <x-form.input name="options[shop_title]" label="Judul hero" :value="$options['shop_title']" :placeholder="'Belanja di '.$settings->displayName()" maxlength="120" />
                    <x-form.input name="options[products_per_page]" type="number" min="4" max="48" label="Produk per halaman" :value="$options['products_per_page']" />
                    <x-form.input name="options[shop_subtitle]" label="Subjudul hero" :value="$options['shop_subtitle']" maxlength="255" class="sm:col-span-2" />
                    <x-form.image name="banner_image" label="Banner toko" :value="\App\Support\MediaUrl::resolve($options['banner_image'])" aspect="aspect-[3/1]" class="sm:col-span-2" />
                </div>
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Opsi toko</h2>
                    <p class="text-xs text-slate-500">CTA dapat diubah lagi per produk.</p>
                </div>
                <div class="card-body grid gap-6 md:grid-cols-2">
                    @foreach ($toggleGroups as $group => $toggles)
                        <fieldset>
                            <legend class="mb-2 text-xs font-semibold tracking-wider text-slate-400 uppercase">{{ $group }}</legend>
                            <div class="space-y-3">
                                @foreach ($toggles as $key => [$label, $help])
                                    <label class="flex cursor-pointer items-start gap-3">
                                        <input type="checkbox" name="options[{{ $key }}]" value="1" class="form-checkbox mt-0.5" @checked($bool($key))>
                                        <span><span class="block text-sm font-medium text-slate-800">{{ $label }}</span><span class="block text-xs text-slate-500">{{ $help }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end"><button class="btn btn-primary"><x-icon name="check" class="size-4" /> Simpan pengaturan</button></div>
        </form>

        <div class="card self-start" x-data="sectionBuilder(@js(route('websites.shop.sections.update', $company)), @js($settings->sectionList()))">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Section halaman Shop</h2>
                    <p class="text-xs text-slate-500">Aktifkan & seret untuk mengurutkan. Tersimpan otomatis.</p>
                </div>
                <span class="text-xs font-medium">
                    <span x-cloak x-show="status === 'saving'" class="text-slate-500">Menyimpan…</span>
                    <span x-cloak x-show="status === 'saved'" class="text-emerald-600">✓ Tersimpan</span>
                    <span x-cloak x-show="status === 'error'" class="text-rose-600">Gagal menyimpan</span>
                </span>
            </div>
            <ul x-ref="list" class="divide-y divide-slate-100">
                <template x-for="section in sections" :key="section.key">
                    <li :data-key="section.key" class="flex items-center gap-3 bg-white px-4 py-3 sm:px-6">
                        <button type="button" data-handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Urutkan"><x-icon name="bars" class="size-5" /></button>
                        <span class="flex-1 text-sm font-medium" :class="section.enabled ? 'text-slate-900' : 'text-slate-400'" x-text="section.label"></span>
                        <button type="button" role="switch" :aria-checked="section.enabled" @click="section.enabled = !section.enabled; save()"
                                class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition" :class="section.enabled ? 'bg-brand-600' : 'bg-slate-200'">
                            <span class="inline-block size-5 translate-y-0.5 rounded-full bg-white shadow transition" :class="section.enabled ? 'translate-x-5.5' : 'translate-x-0.5'"></span>
                        </button>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</x-website-layout>
