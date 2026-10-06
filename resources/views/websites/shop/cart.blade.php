@extends('websites.shop.layout')

@php($fmt = fn ($v) => \App\Support\Shop\Money::format($v))

@section('shop')
    @include('websites.shop.partials.page-header', ['title' => 'Keranjang Belanja', 'crumbs' => ['Keranjang' => null], 'subtitle' => $summary['count'] ? $summary['count'].' barang di keranjang Anda' : null])

    <div class="{{ $ds->container() }} py-8 sm:py-12">
        @if (! $summary['lines'])
            @include('websites.shop.partials.empty', [
                'emptyTitle' => 'Keranjang Anda masih kosong',
                'emptyText' => 'Yuk, temukan produk favorit Anda dan tambahkan ke keranjang.',
                'actionUrl' => $site->shop('products'),
            ])
        @else
            @if ($summary['errors'])
                <div class="mb-6 rounded-brand border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
                    <p class="flex items-center gap-2 font-semibold"><x-icon name="warning" class="size-4" /> Periksa kembali keranjang Anda</p>
                    <ul class="mt-1 list-disc pl-6">
                        @foreach ($summary['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-[1fr_22rem] lg:items-start">
                {{-- Lines --}}
                <div class="min-w-0">
                    <ul class="divide-y divide-line border-y border-line">
                        @foreach ($summary['lines'] as $line)
                            @php($productUrl = $site->shop('product/'.$line['product']->slug))
                            <li class="flex gap-3 py-5 sm:gap-5">
                                <a href="{{ $productUrl }}" class="size-20 shrink-0 overflow-hidden rounded-brand bg-surface-alt sm:size-28">
                                    <x-site.img :src="$line['image']" :alt="$line['name']" icon="cube" class="size-full object-cover" />
                                </a>
                                <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                    <div class="min-w-0">
                                        <a href="{{ $productUrl }}" class="line-clamp-2 font-semibold text-ink hover:text-primary">{{ $line['name'] }}</a>
                                        @if ($line['variant_label'])
                                            <p class="text-sm text-muted">{{ $line['variant_label'] }}</p>
                                        @endif
                                        <p class="mt-1 text-sm">
                                            <span class="font-semibold text-ink">{{ $fmt($line['unit_price']) }}</span>
                                            @if ($line['original_price'])
                                                <span class="ml-1 text-xs text-muted line-through">{{ $fmt($line['original_price']) }}</span>
                                            @endif
                                        </p>
                                        @if ($line['error'])
                                            <p class="mt-1 text-xs font-medium text-rose-600">{{ $line['error'] }}</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                        <form method="POST" action="{{ $site->shop('cart/'.$line['id']) }}" class="flex h-10 items-center rounded-btn border border-line bg-card">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="sr-only">Perbarui jumlah</button>
                                            <label for="qty-{{ $line['id'] }}" class="sr-only">Jumlah {{ $line['name'] }}</label>
                                            <input id="qty-{{ $line['id'] }}" type="number" name="quantity" value="{{ $line['quantity'] }}" min="0" max="{{ $line['available'] ?? 99 }}" inputmode="numeric"
                                                   @change="$el.form.requestSubmit()" class="shop-qty order-2 h-full w-11 border-0 bg-transparent text-center text-sm font-semibold text-ink focus:outline-none">
                                            <button type="submit" name="quantity" value="{{ $line['quantity'] - 1 }}" class="order-1 inline-flex h-full w-9 items-center justify-center text-ink hover:text-primary" aria-label="Kurangi"><x-shop.icon name="minus" class="size-3.5" /></button>
                                            <button type="submit" name="quantity" value="{{ $line['quantity'] + 1 }}" class="order-3 inline-flex h-full w-9 items-center justify-center text-ink hover:text-primary disabled:opacity-30" aria-label="Tambah" @disabled($line['available'] !== null && $line['quantity'] >= $line['available'])><x-icon name="plus" class="size-3.5" /></button>
                                        </form>
                                        <div class="flex items-center gap-3 sm:flex-row-reverse">
                                            <span class="font-heading font-bold text-ink">{{ $fmt($line['line_total']) }}</span>
                                            <form method="POST" action="{{ $site->shop('cart/'.$line['id']) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex size-9 items-center justify-center rounded-full text-muted hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus {{ $line['name'] }}"><x-icon name="trash" class="size-4" /></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $site->shop('products') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary"><x-icon name="arrow-left" class="size-4" /> Lanjut belanja</a>
                </div>

                {{-- Summary --}}
                <aside class="{{ $ds->card('p-5 sm:p-6 lg:sticky lg:top-28', false) }} border border-line" aria-label="Ringkasan belanja">
                    <h2 class="font-heading text-lg font-bold text-ink">Ringkasan</h2>

                    {{-- Coupon --}}
                    <div class="mt-4">
                        @if ($summary['coupon'])
                            <div class="flex items-center justify-between gap-3 rounded-brand border border-dashed border-emerald-400 bg-emerald-50 px-3 py-2.5 text-sm text-emerald-800">
                                <span class="flex min-w-0 items-center gap-2"><x-icon name="tag" class="size-4 shrink-0" /> <span class="truncate"><b>{{ $summary['coupon']->code }}</b> dipakai</span></span>
                                <form method="POST" action="{{ $site->shop('cart/coupon') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold underline">Hapus</button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ $site->shop('cart/coupon') }}" class="flex gap-2">
                                @csrf
                                <label for="coupon" class="sr-only">Kode kupon</label>
                                <input id="coupon" name="code" value="{{ old('code') }}" placeholder="Kode kupon" maxlength="40" class="shop-input min-w-0 flex-1 uppercase" @if ($errors->has('code')) aria-invalid="true" @endif>
                                <button type="submit" class="{{ $ds->btn('secondary', '!px-4 !py-2.5') }}">Pakai</button>
                            </form>
                            @error('code')
                                <p class="shop-error">{{ $message }}</p>
                            @enderror
                            @if ($summary['coupon_error'])
                                <p class="shop-error">{{ $summary['coupon_error'] }}</p>
                            @endif
                        @endif
                    </div>

                    <dl class="mt-5 space-y-2.5 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal ({{ $summary['count'] }} barang)</dt><dd class="font-semibold text-ink">{{ $fmt($summary['subtotal']) }}</dd></div>
                        @if ($summary['discount'] > 0)
                            <div class="flex justify-between gap-4"><dt class="text-muted">Diskon</dt><dd class="font-semibold text-emerald-600">-{{ $fmt($summary['discount']) }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-4"><dt class="text-muted">Ongkos kirim</dt><dd class="text-muted">Dihitung saat checkout</dd></div>
                        @if ($summary['tax_name'])
                            <div class="flex justify-between gap-4"><dt class="text-muted">{{ $summary['tax_name'] }}{{ $summary['tax_inclusive'] ? ' (termasuk)' : '' }}</dt><dd class="text-ink">{{ $fmt($summary['tax']) }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-4 border-t border-line pt-3 text-base"><dt class="font-semibold text-ink">Total</dt><dd class="font-heading text-xl font-bold text-ink">{{ $fmt($summary['total']) }}</dd></div>
                    </dl>

                    @if (($minOrder = $shop->option('min_order')) && $summary['subtotal'] < (float) $minOrder)
                        <p class="mt-4 rounded-md bg-amber-50 px-3 py-2 text-xs text-amber-800">Minimal pembelian {{ $fmt($minOrder) }}. Tambah {{ $fmt((float) $minOrder - $summary['subtotal']) }} lagi untuk checkout.</p>
                    @endif

                    <a href="{{ $site->shop('checkout') }}" class="{{ $ds->btn('primary', 'mt-5 w-full !py-3.5') }}">Lanjut ke Checkout <x-icon name="arrow-right" class="size-4" /></a>
                    <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-muted"><x-icon name="lock" class="size-3.5" /> Harga & stok divalidasi ulang saat checkout</p>
                </aside>
            </div>
        @endif
    </div>
@endsection
