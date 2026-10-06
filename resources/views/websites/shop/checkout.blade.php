@extends('websites.shop.layout')

@php
    $fmt = fn ($v) => \App\Support\Shop\Money::format($v);

    // Initial shipping options (no address yet) so the form also works without JavaScript.
    $initialQuotes = app(\App\Services\Shop\Shipping\ShippingService::class)->quotes($company, new \App\Services\Shop\Shipping\ShippingContext(
        subtotal: $summary['subtotal'] - $summary['discount'],
        weightGrams: $summary['weight'],
        quantity: $summary['count'],
        city: old('address.city'),
        province: old('address.province'),
        postalCode: old('address.postal_code'),
        freeShippingCoupon: $summary['coupon']?->type === 'free_shipping',
    ))->map(fn ($q) => $q->toArray() + ['cost_formatted' => $q->cost > 0 ? $fmt($q->cost) : 'Gratis'])->values();

    $defaultAddress = $customer?->defaultAddress();
    $addr = fn (string $key) => old('address.'.$key, $defaultAddress?->{$key} ?? ($key === 'name' ? $prefill['name'] : ($key === 'phone' ? $prefill['phone'] : null)));
    $requirePhone = (bool) $shop->option('require_phone');
    $waCheckout = $shop->option('whatsapp_checkout') && $company->whatsappUrl();
    $guestAllowed = $customer || $shop->option('guest_checkout', true);

    $startStep = match (true) {
        $errors->hasAny(['name', 'email', 'phone', 'whatsapp']) => 1,
        $errors->hasAny(['address.name', 'address.phone', 'address.address', 'address.city', 'address.province', 'address.postal_code', 'shipping_method_id']) => 2,
        $errors->hasAny(['payment_method_id', 'coupon', 'notes']) => 3,
        $errors->any() => 4,
        default => 1,
    };

    $config = [
        'quoteUrl' => $site->shop('checkout/quote'),
        'shippingMethods' => $initialQuotes,
        'shippingId' => old('shipping_method_id'),
        'paymentId' => old('payment_method_id', $paymentMethods->first()?->id),
        'coupon' => old('coupon', $summary['coupon']?->code),
        'startStep' => $startStep,
        'totals' => [
            'subtotal' => $fmt($summary['subtotal']),
            'discount' => $summary['discount'] > 0 ? '-'.$fmt($summary['discount']) : null,
            'coupon' => $summary['coupon']?->code,
            'coupon_error' => null,
            'shipping' => '—',
            'tax' => $summary['tax'] > 0 ? $fmt($summary['tax']) : null,
            'tax_label' => $summary['tax_name'] ? $summary['tax_name'].($summary['tax_inclusive'] ? ' (termasuk)' : '') : null,
            'total' => $fmt($summary['total']),
        ],
    ];
    $stepLabels = ['Kontak', 'Pengiriman', 'Pembayaran', 'Konfirmasi'];
@endphp

@section('shop')
    @include('websites.shop.partials.page-header', ['title' => 'Checkout', 'crumbs' => ['Keranjang' => $site->shop('cart'), 'Checkout' => null]])

    <div class="{{ $ds->container() }} py-8 sm:py-12">
        @unless ($guestAllowed)
            <div class="mx-auto max-w-lg">
                @include('websites.shop.partials.empty', [
                    'icon' => 'lock',
                    'emptyTitle' => 'Masuk untuk melanjutkan checkout',
                    'emptyText' => 'Toko ini mewajibkan akun pelanggan. Masuk atau daftar gratis — isi keranjang Anda tetap tersimpan.',
                    'actionUrl' => $site->account('login'),
                    'actionLabel' => 'Masuk / Daftar',
                ])
            </div>
        @else
            <form method="POST" action="{{ $site->shop('checkout') }}" x-data="checkout(@js($config))" class="grid gap-8 lg:grid-cols-[1fr_24rem] lg:items-start" novalidate>
                @csrf
                <div class="min-w-0">
                    {{-- Stepper --}}
                    <ol x-cloak x-show="enhanced" class="mb-8 grid grid-cols-4 gap-2 text-center text-[11px] font-semibold sm:text-xs">
                        @foreach ($stepLabels as $i => $label)
                            <li>
                                <button type="button" @click="goto({{ $i + 1 }})" class="flex w-full flex-col items-center gap-2" :class="step >= {{ $i + 1 }} ? 'text-ink' : 'text-muted'" :disabled="step <= {{ $i + 1 }}">
                                    <span class="flex w-full items-center">
                                        <span class="h-0.5 flex-1 {{ $i === 0 ? 'opacity-0' : '' }}" :class="step >= {{ $i + 1 }} ? 'bg-primary' : 'bg-line'"></span>
                                        <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full border-2 text-xs transition"
                                              :class="step > {{ $i + 1 }} ? 'border-primary bg-primary text-on-primary' : (step === {{ $i + 1 }} ? 'border-primary text-primary' : 'border-line text-muted')">
                                            <span x-show="step <= {{ $i + 1 }}">{{ $i + 1 }}</span>
                                            <x-icon name="check" class="size-4" x-show="step > {{ $i + 1 }}" />
                                        </span>
                                        <span class="h-0.5 flex-1 {{ $i === 3 ? 'opacity-0' : '' }}" :class="step > {{ $i + 1 }} ? 'bg-primary' : 'bg-line'"></span>
                                    </span>
                                    {{ $label }}
                                </button>
                            </li>
                        @endforeach
                    </ol>

                    @if ($errors->any())
                        <div class="mb-6 rounded-brand border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                            <p class="font-semibold">Pesanan belum bisa dibuat:</p>
                            <ul class="mt-1 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($summary['errors'])
                        <div class="mb-6 rounded-brand border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            @foreach ($summary['errors'] as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                            <a href="{{ $site->shop('cart') }}" class="mt-1 inline-block font-semibold underline">Perbaiki keranjang</a>
                        </div>
                    @endif

                    {{-- 1. Contact --}}
                    <fieldset data-step="1" x-show="!enhanced || step === 1" class="{{ $ds->card('p-5 sm:p-6', false) }} border border-line">
                        <legend class="sr-only">Informasi kontak</legend>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="font-heading text-lg font-bold text-ink">1. Informasi Kontak</h2>
                            @unless ($customer)
                                <a href="{{ $site->account('login') }}" class="text-sm font-semibold text-primary">Sudah punya akun? Masuk</a>
                            @endunless
                        </div>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="co-name" class="shop-label">Nama lengkap *</label>
                                <input id="co-name" name="name" value="{{ $prefill['name'] }}" required maxlength="120" autocomplete="name" class="shop-input" @error('name') aria-invalid="true" @enderror>
                                @error('name')<p class="shop-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="co-email" class="shop-label">Email *</label>
                                <input id="co-email" type="email" name="email" value="{{ $prefill['email'] }}" required maxlength="150" autocomplete="email" class="shop-input" @error('email') aria-invalid="true" @enderror>
                                @error('email')<p class="shop-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="co-phone" class="shop-label">No. HP {{ $requirePhone ? '*' : '' }}</label>
                                <input id="co-phone" type="tel" name="phone" value="{{ $prefill['phone'] }}" @required($requirePhone) maxlength="30" pattern="[0-9+\s\-\(\)]+" autocomplete="tel" placeholder="08xxxxxxxxxx" class="shop-input" @error('phone') aria-invalid="true" @enderror>
                                @error('phone')<p class="shop-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="co-wa" class="shop-label">WhatsApp <span class="font-normal text-muted">(opsional, jika berbeda)</span></label>
                                <input id="co-wa" type="tel" name="whatsapp" value="{{ $prefill['whatsapp'] }}" maxlength="30" pattern="[0-9+\s\-\(\)]+" class="shop-input">
                            </div>
                        </div>
                        <div x-cloak x-show="enhanced" class="mt-6 flex justify-end">
                            <button type="button" @click="next()" class="{{ $ds->btn('primary') }}">Lanjut ke Pengiriman <x-icon name="arrow-right" class="size-4" /></button>
                        </div>
                    </fieldset>

                    {{-- 2. Shipping: method + address --}}
                    <fieldset data-step="2" x-show="!enhanced || step === 2" class="{{ $ds->card('mt-6 p-5 sm:p-6', false) }} border border-line">
                        <legend class="sr-only">Pengiriman</legend>
                        <h2 class="font-heading text-lg font-bold text-ink">2. Pengiriman</h2>

                        {{-- Shipping methods --}}
                        <div class="mt-5">
                            <p class="shop-label">Metode pengiriman *</p>
                            <div class="space-y-2.5" :class="quoting && 'opacity-60'">
                                <noscript>
                                    @forelse ($initialQuotes as $q)
                                        <label class="mb-2.5 flex cursor-pointer items-start gap-3 rounded-brand border border-line p-3.5">
                                            <input type="radio" name="shipping_method_id" value="{{ $q['methodId'] }}" @checked((int) old('shipping_method_id') === $q['methodId']) required class="mt-1 accent-[var(--brand-primary)]">
                                            <span class="flex-1 text-sm"><b class="text-ink">{{ $q['name'] }}</b> <span class="block text-muted">{{ $q['description'] }} {{ $q['estimate'] ? '· '.$q['estimate'] : '' }}</span></span>
                                            <span class="text-sm font-semibold text-ink">{{ $q['cost_formatted'] }}</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-muted">Belum ada metode pengiriman.</p>
                                    @endforelse
                                </noscript>
                                <template x-for="m in shippingMethods" :key="m.methodId">
                                    <label class="flex cursor-pointer items-start gap-3 rounded-brand border p-3.5 transition" :class="shippingId === Number(m.methodId) ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-line hover:border-ink/40'">
                                        <input type="radio" name="shipping_method_id" :value="m.methodId" :checked="shippingId === Number(m.methodId)" @change="chooseShipping(m.methodId)" class="mt-1 accent-[var(--brand-primary)]">
                                        <span class="min-w-0 flex-1 text-sm">
                                            <span class="block font-semibold text-ink" x-text="m.name"></span>
                                            <span class="block text-muted" x-text="[m.description, m.estimate].filter(Boolean).join(' · ')"></span>
                                        </span>
                                        <span class="shrink-0 text-sm font-semibold" :class="m.cost > 0 ? 'text-ink' : 'text-emerald-600'" x-text="m.cost_formatted"></span>
                                    </label>
                                </template>
                                <p x-cloak x-show="enhanced && !quoting && shippingMethods.length === 0" class="rounded-brand border border-dashed border-line p-4 text-sm text-muted">Tidak ada metode pengiriman untuk alamat ini. Coba kota lain atau gunakan checkout via WhatsApp.</p>
                            </div>
                            @error('shipping_method_id')<p class="shop-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Address --}}
                        <div class="mt-6" x-show="!enhanced || needsAddress">
                            <p class="shop-label">Alamat pengiriman</p>
                            @if ($addresses->isNotEmpty())
                                <div x-cloak x-show="enhanced" class="shop-scroll -mx-1 mb-4 flex gap-2.5 overflow-x-auto px-1 pb-1">
                                    @foreach ($addresses as $a)
                                        <button type="button" @click="useAddress(@js($a->only(['id', 'name', 'phone', 'address', 'city', 'province', 'postal_code', 'country'])))"
                                                class="w-56 shrink-0 rounded-brand border p-3 text-left text-xs transition"
                                                :class="addressId === {{ $a->id }} ? 'border-primary ring-1 ring-primary' : 'border-line hover:border-ink/40'">
                                            <span class="flex items-center justify-between gap-2"><b class="truncate text-sm text-ink">{{ $a->label ?: $a->name }}</b> @if ($a->is_default)<span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Utama</span>@endif</span>
                                            <span class="mt-1 line-clamp-2 block text-muted">{{ $a->oneLine() }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="ad-name" class="shop-label">Nama penerima</label>
                                    <input id="ad-name" name="address[name]" value="{{ $addr('name') }}" maxlength="120" class="shop-input">
                                </div>
                                <div>
                                    <label for="ad-phone" class="shop-label">No. HP penerima</label>
                                    <input id="ad-phone" type="tel" name="address[phone]" value="{{ $addr('phone') }}" maxlength="30" class="shop-input">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="ad-address" class="shop-label">Alamat lengkap *</label>
                                    <textarea id="ad-address" name="address[address]" rows="2" maxlength="500" :required="needsAddress" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" class="shop-input" @error('address.address') aria-invalid="true" @enderror>{{ $addr('address') }}</textarea>
                                    @error('address.address')<p class="shop-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="ad-city" class="shop-label">Kota / Kabupaten *</label>
                                    <input id="ad-city" name="address[city]" value="{{ $addr('city') }}" maxlength="100" :required="needsAddress" @change="quote()" class="shop-input" @error('address.city') aria-invalid="true" @enderror>
                                    @error('address.city')<p class="shop-error">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="ad-province" class="shop-label">Provinsi</label>
                                    <input id="ad-province" name="address[province]" value="{{ $addr('province') }}" maxlength="100" @change="quote()" class="shop-input">
                                </div>
                                <div>
                                    <label for="ad-postal" class="shop-label">Kode pos</label>
                                    <input id="ad-postal" name="address[postal_code]" value="{{ $addr('postal_code') }}" maxlength="20" inputmode="numeric" @change="quote()" class="shop-input">
                                </div>
                                <div>
                                    <label for="ad-country" class="shop-label">Negara</label>
                                    <input id="ad-country" name="address[country]" value="{{ $addr('country') ?: 'Indonesia' }}" maxlength="100" class="shop-input">
                                </div>
                            </div>
                            @if ($customer)
                                <label class="mt-4 flex items-center gap-2.5 text-sm text-ink">
                                    <input type="checkbox" name="save_address" value="1" @checked(old('save_address', $addresses->isEmpty())) class="size-4 rounded accent-[var(--brand-primary)]"> Simpan alamat ini ke akun saya
                                </label>
                            @endif
                        </div>
                        <p x-cloak x-show="enhanced && !needsAddress" class="mt-5 rounded-brand bg-surface-alt px-4 py-3 text-sm text-muted">Pesanan diambil langsung di toko — alamat pengiriman tidak diperlukan.</p>

                        <div x-cloak x-show="enhanced" class="mt-6 flex flex-wrap justify-between gap-3">
                            <button type="button" @click="prev()" class="{{ $ds->btn('ghost') }}"><x-icon name="arrow-left" class="size-4" /> Kembali</button>
                            <button type="button" @click="next()" class="{{ $ds->btn('primary') }}">Lanjut ke Pembayaran <x-icon name="arrow-right" class="size-4" /></button>
                        </div>
                    </fieldset>

                    {{-- 3. Payment --}}
                    <fieldset data-step="3" x-show="!enhanced || step === 3" class="{{ $ds->card('mt-6 p-5 sm:p-6', false) }} border border-line">
                        <legend class="sr-only">Pembayaran</legend>
                        <h2 class="font-heading text-lg font-bold text-ink">3. Pembayaran</h2>
                        <div class="mt-5 space-y-2.5">
                            @forelse ($paymentMethods as $pm)
                                <label class="block cursor-pointer rounded-brand border p-3.5 transition" :class="paymentId === {{ $pm->id }} ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-line hover:border-ink/40'">
                                    <span class="flex items-start gap-3">
                                        <input type="radio" name="payment_method_id" value="{{ $pm->id }}" x-model.number="paymentId" @checked((int) old('payment_method_id', $paymentMethods->first()->id) === $pm->id) required class="mt-1 accent-[var(--brand-primary)]">
                                        <span class="min-w-0 flex-1 text-sm">
                                            <span class="flex items-center gap-2 font-semibold text-ink">
                                                <x-icon :name="$pm->type === 'cod' ? 'banknotes' : 'credit-card'" class="size-4 text-primary" /> {{ $pm->name }}
                                            </span>
                                            @if ($pm->instructions)
                                                <span class="mt-1 block text-muted">{{ $pm->instructions }}</span>
                                            @endif
                                            @if ($pm->type === 'bank_transfer' && ! empty($pm->config['account_number']))
                                                <span class="mt-2 block rounded-md bg-surface-alt px-3 py-2 text-xs text-ink" x-show="!enhanced || paymentId === {{ $pm->id }}">
                                                    {{ $pm->config['bank'] ?? 'Bank' }} · <b class="font-mono">{{ $pm->config['account_number'] }}</b> a.n. {{ $pm->config['account_name'] ?? $company->name }}
                                                    <span class="block text-muted">Detail lengkap juga tampil di halaman pesanan.</span>
                                                </span>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <p class="rounded-brand border border-dashed border-line p-4 text-sm text-muted">Penjual belum mengaktifkan metode pembayaran. Silakan checkout via WhatsApp.</p>
                            @endforelse
                            @error('payment_method_id')<p class="shop-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-6">
                            <label for="co-coupon" class="shop-label">Kode kupon</label>
                            <div class="flex gap-2">
                                <input id="co-coupon" name="coupon" x-model="coupon" value="{{ $config['coupon'] }}" maxlength="40" class="shop-input min-w-0 flex-1 uppercase" placeholder="Opsional">
                                <button type="button" x-cloak x-show="enhanced" @click="quote()" class="{{ $ds->btn('secondary', '!px-4 !py-2.5') }}">Cek</button>
                            </div>
                            <p class="shop-error" x-show="couponError" x-text="couponError"></p>
                            <p class="mt-1.5 text-xs font-medium text-emerald-600" x-cloak x-show="totals.coupon && !couponError" x-text="'Kupon ' + totals.coupon + ' diterapkan'"></p>
                            @error('coupon')<p class="shop-error">{{ $message }}</p>@enderror
                        </div>

                        @if ($shop->option('order_note'))
                            <div class="mt-5">
                                <label for="co-notes" class="shop-label">Catatan untuk penjual <span class="font-normal text-muted">(opsional)</span></label>
                                <textarea id="co-notes" name="notes" rows="2" maxlength="1000" class="shop-input" placeholder="Contoh: warna cadangan, jam pengiriman…">{{ old('notes') }}</textarea>
                            </div>
                        @endif

                        <div x-cloak x-show="enhanced" class="mt-6 flex flex-wrap justify-between gap-3">
                            <button type="button" @click="prev()" class="{{ $ds->btn('ghost') }}"><x-icon name="arrow-left" class="size-4" /> Kembali</button>
                            <button type="button" @click="next()" class="{{ $ds->btn('primary') }}">Tinjau Pesanan <x-icon name="arrow-right" class="size-4" /></button>
                        </div>
                    </fieldset>

                    {{-- 4. Review & place order --}}
                    <fieldset data-step="4" x-show="!enhanced || step === 4" class="{{ $ds->card('mt-6 p-5 sm:p-6', false) }} border border-line">
                        <legend class="sr-only">Konfirmasi</legend>
                        <h2 class="font-heading text-lg font-bold text-ink">4. Konfirmasi Pesanan</h2>

                        <dl x-cloak x-show="enhanced" class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                            <div class="rounded-brand bg-surface-alt p-4">
                                <dt class="flex items-center justify-between text-xs font-semibold tracking-wide text-muted uppercase">Kontak <button type="button" @click="step = 1" class="normal-case text-primary">Ubah</button></dt>
                                <dd class="mt-1.5 text-ink" x-text="step === 4 ? [field('name')?.value, field('email')?.value, field('phone')?.value].filter(Boolean).join(' · ') : ''"></dd>
                            </div>
                            <div class="rounded-brand bg-surface-alt p-4">
                                <dt class="flex items-center justify-between text-xs font-semibold tracking-wide text-muted uppercase">Pengiriman <button type="button" @click="step = 2" class="normal-case text-primary">Ubah</button></dt>
                                <dd class="mt-1.5 text-ink">
                                    <span class="block font-semibold" x-text="selectedShipping ? selectedShipping.name + ' — ' + selectedShipping.cost_formatted : '-'"></span>
                                    <span class="block text-muted" x-show="needsAddress" x-text="step === 4 ? [field('address[address]')?.value, field('address[city]')?.value, field('address[province]')?.value].filter(Boolean).join(', ') : ''"></span>
                                </dd>
                            </div>
                            <div class="rounded-brand bg-surface-alt p-4 sm:col-span-2">
                                <dt class="flex items-center justify-between text-xs font-semibold tracking-wide text-muted uppercase">Pembayaran <button type="button" @click="step = 3" class="normal-case text-primary">Ubah</button></dt>
                                <dd class="mt-1.5 text-ink" x-text="(@js($paymentMethods->pluck('name', 'id'))[paymentId]) || '-'"></dd>
                            </div>
                        </dl>

                        <label class="mt-5 flex items-start gap-2.5 text-sm text-ink">
                            <input type="checkbox" name="terms" value="1" required @checked(old('terms')) class="mt-0.5 size-4 rounded accent-[var(--brand-primary)]">
                            <span>Saya sudah memeriksa pesanan dan menyetujui ketentuan belanja di {{ $company->name }}.</span>
                        </label>
                        @error('terms')<p class="shop-error">{{ $message }}</p>@enderror

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row-reverse sm:items-center sm:justify-between">
                            <button type="submit" class="{{ $ds->btn('primary', '!py-3.5 sm:min-w-56') }}" @click="if (enhanced && !validStep(4)) $event.preventDefault()">
                                <x-icon name="lock" class="size-4" /> Buat Pesanan
                            </button>
                            <button type="button" x-cloak x-show="enhanced" @click="prev()" class="{{ $ds->btn('ghost') }}"><x-icon name="arrow-left" class="size-4" /> Kembali</button>
                        </div>

                        @if ($waCheckout)
                            <div class="mt-6 border-t border-line pt-5">
                                <p class="text-sm text-muted">Lebih nyaman konfirmasi lewat chat? Pesanan dibuat lalu Anda bisa mengirim detailnya ke penjual via WhatsApp (pesan tidak terkirim otomatis).</p>
                                <button type="submit" formaction="{{ $site->shop('checkout/whatsapp') }}" formnovalidate
                                        @click="if (enhanced && !validStep(1)) { $event.preventDefault(); step = 1 }"
                                        class="ds-btn mt-3 w-full bg-[#25D366] text-white hover:brightness-95 sm:w-auto">
                                    <x-icon name="whatsapp" class="size-5" /> Checkout via WhatsApp
                                </button>
                            </div>
                        @endif
                    </fieldset>
                </div>

                {{-- Order summary --}}
                <aside class="{{ $ds->card('p-5 sm:p-6 lg:sticky lg:top-28', false) }} border border-line" aria-label="Ringkasan pesanan">
                    <h2 class="flex items-center justify-between font-heading text-lg font-bold text-ink">Ringkasan <a href="{{ $site->shop('cart') }}" class="text-xs font-semibold text-primary">Ubah keranjang</a></h2>
                    <ul class="mt-4 max-h-72 space-y-3 overflow-y-auto pr-1">
                        @foreach ($summary['lines'] as $line)
                            <li class="flex gap-3">
                                <span class="relative size-14 shrink-0 rounded-brand bg-surface-alt">
                                    <x-site.img :src="$line['image']" :alt="$line['name']" icon="cube" class="size-full rounded-brand object-cover" />
                                    <span class="absolute -top-1.5 -right-1.5 inline-flex size-5 items-center justify-center rounded-full bg-ink text-[10px] font-bold text-surface">{{ $line['quantity'] }}</span>
                                </span>
                                <span class="min-w-0 flex-1 text-sm">
                                    <span class="line-clamp-2 font-medium text-ink">{{ $line['name'] }}</span>
                                    @if ($line['variant_label'])<span class="block text-xs text-muted">{{ $line['variant_label'] }}</span>@endif
                                </span>
                                <span class="shrink-0 text-sm font-semibold text-ink">{{ $fmt($line['line_total']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="mt-5 space-y-2 border-t border-line pt-4 text-sm" :class="quoting && 'opacity-60'">
                        <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal</dt><dd class="font-semibold text-ink" x-text="totals.subtotal">{{ $config['totals']['subtotal'] }}</dd></div>
                        <div class="flex justify-between gap-4" x-show="totals.discount" @if (! $config['totals']['discount']) x-cloak @endif><dt class="text-muted">Diskon <span x-text="totals.coupon ? '(' + totals.coupon + ')' : ''"></span></dt><dd class="font-semibold text-emerald-600" x-text="totals.discount">{{ $config['totals']['discount'] }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-muted">Ongkos kirim</dt><dd class="text-ink" x-text="totals.shipping">—</dd></div>
                        <div class="flex justify-between gap-4" x-show="totals.tax" @if (! $config['totals']['tax']) x-cloak @endif><dt class="text-muted" x-text="totals.tax_label">{{ $config['totals']['tax_label'] }}</dt><dd class="text-ink" x-text="totals.tax">{{ $config['totals']['tax'] }}</dd></div>
                        <div class="flex justify-between gap-4 border-t border-line pt-3 text-base"><dt class="font-semibold text-ink">Total</dt><dd class="font-heading text-xl font-bold text-ink" x-text="totals.total">{{ $config['totals']['total'] }}</dd></div>
                    </dl>
                    <p class="mt-4 flex items-center gap-1.5 text-xs text-muted"><x-icon name="shield" class="size-4 text-primary" /> Total final dihitung ulang oleh sistem saat pesanan dibuat.</p>
                </aside>
            </form>
        @endunless
    </div>
@endsection
