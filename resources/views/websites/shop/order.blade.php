@php
    $inAccount = $inAccount ?? false;
    $accountTitle = 'Pesanan #'.$order->order_number;
    $accountSubtitle = 'Dibuat '.$order->created_at->translatedFormat('d F Y, H:i');
    $money = fn ($v) => \App\Support\Shop\Money::format($v, $order->currency);
    $instructions = $order->payment?->instructions ?? [];
    $address = $order->shipping_address ?? [];
@endphp

@extends($inAccount ? 'websites.shop.account.layout' : 'websites.shop.layout')

@section($inAccount ? 'account' : 'shop')
    @unless ($inAccount)
        @include('websites.shop.partials.page-header', ['title' => 'Pesanan #'.$order->order_number, 'subtitle' => 'Dibuat '.$order->created_at->translatedFormat('d F Y, H:i'), 'crumbs' => ['Pesanan' => null]])
    @endunless

    <div class="{{ $inAccount ? '' : $ds->container().' py-8 sm:py-12' }}">
        <div class="space-y-6">
            @if ($justPlaced && $order->status !== 'cancelled')
                <div class="relative overflow-hidden rounded-[calc(var(--brand-radius)*1.5)] bg-primary px-6 py-8 text-on-primary sm:px-10">
                    <div class="pointer-events-none absolute -top-10 -right-10 size-48 rounded-full bg-white/10"></div>
                    <x-icon name="check-circle" class="size-10" />
                    <h2 class="heading mt-3 text-2xl sm:text-3xl">Terima kasih, {{ $order->customer_name }}!</h2>
                    <p class="mt-2 max-w-2xl text-sm text-on-primary/85 sm:text-base">
                        Pesanan <b>#{{ $order->order_number }}</b> berhasil dibuat. Penjual akan memproses pesanan dan menghubungi Anda melalui {{ $order->customer_phone ?: $order->customer_email }}.
                        Simpan tautan halaman ini untuk memantau status pesanan Anda.
                    </p>
                </div>
            @endif

            @if ($whatsappUrl)
                <div class="flex flex-col gap-4 rounded-brand border border-[#25D366]/40 bg-[#25D366]/10 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex gap-3">
                        <x-icon name="whatsapp" class="size-8 shrink-0 text-[#1da851]" />
                        <div>
                            <p class="font-semibold text-ink">Langkah terakhir: kirim pesanan via WhatsApp</p>
                            <p class="text-sm text-muted">Klik tombol untuk membuka WhatsApp dengan detail pesanan yang sudah terisi. Pesan baru terkirim setelah Anda menekan kirim.</p>
                        </div>
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="ds-btn shrink-0 bg-[#25D366] text-white hover:brightness-95">
                        <x-icon name="whatsapp" class="size-5" /> Buka WhatsApp
                    </a>
                </div>
            @endif

            {{-- Status --}}
            <section class="{{ $ds->card('p-5 sm:p-6', false) }} border border-line">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-muted">Status:</span>
                        @include('websites.shop.partials.status-badge', ['status' => $order->status])
                        <span class="text-sm text-muted">Pembayaran:</span>
                        @include('websites.shop.partials.status-badge', ['status' => $order->payment_status === 'pending' ? 'pending' : $order->payment_status])
                    </div>
                    <button type="button" onclick="window.print()" class="shop-no-print inline-flex items-center gap-1.5 text-sm font-semibold text-primary"><x-shop.icon name="printer" class="size-4" /> Cetak / simpan PDF</button>
                </div>
                <div class="mt-6">@include('websites.shop.partials.timeline')</div>

                @if ($order->shipment && ($order->shipment->tracking_number || $order->shipment->courier))
                    <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-brand bg-surface-alt px-4 py-3 text-sm">
                        <span class="flex items-center gap-2 text-muted"><x-icon name="truck" class="size-5 text-primary" /> {{ $order->shipment->courier ?: $order->shipment->method }}</span>
                        @if ($order->shipment->tracking_number)
                            <span class="text-ink">No. resi: <b class="font-mono select-all">{{ $order->shipment->tracking_number }}</b></span>
                        @endif
                        @if ($order->shipment->delivered_at)
                            <span class="text-emerald-600">Diterima {{ $order->shipment->delivered_at->translatedFormat('d M Y') }}</span>
                        @endif
                    </div>
                @endif
            </section>

            {{-- Payment instructions --}}
            @if ($order->payment && $order->payment_status === 'pending' && ! in_array($order->status, ['cancelled', 'refunded'], true) && $instructions)
                <section class="rounded-brand border-2 border-dashed border-primary/40 bg-primary/5 p-5 sm:p-6">
                    <h2 class="flex items-center gap-2 font-heading text-lg font-bold text-ink"><x-icon name="credit-card" class="size-5 text-primary" /> Instruksi Pembayaran — {{ $instructions['title'] ?? $order->payment_method }}</h2>
                    @if (! empty($instructions['text']))
                        <p class="mt-2 text-sm text-muted">{{ $instructions['text'] }}</p>
                    @endif
                    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                        @if (! empty($instructions['bank']))
                            <div><dt class="text-muted">Bank</dt><dd class="font-semibold text-ink">{{ $instructions['bank'] }}</dd></div>
                        @endif
                        @if (! empty($instructions['account_number']))
                            <div x-data="{ copied: false }"><dt class="text-muted">No. rekening</dt>
                                <dd class="flex items-center gap-2 font-mono text-base font-bold text-ink">{{ $instructions['account_number'] }}
                                    <button type="button" class="shop-no-print rounded-md border border-line px-2 py-0.5 font-body text-xs font-semibold text-primary" @click="navigator.clipboard.writeText(@js($instructions['account_number'])); copied = true; setTimeout(() => copied = false, 2000)" x-text="copied ? 'Disalin' : 'Salin'">Salin</button>
                                </dd>
                            </div>
                        @endif
                        @if (! empty($instructions['account_name']))
                            <div><dt class="text-muted">Atas nama</dt><dd class="font-semibold text-ink">{{ $instructions['account_name'] }}</dd></div>
                        @endif
                        <div><dt class="text-muted">Jumlah transfer</dt><dd class="font-heading text-xl font-bold text-primary">{{ $money($instructions['amount'] ?? $order->total) }}</dd></div>
                    </dl>
                    @if ($company->whatsappUrl() && $order->payment->method === 'bank_transfer')
                        <a href="{{ $company->whatsappUrl() }}?text={{ rawurlencode('Halo, saya sudah transfer untuk pesanan #'.$order->order_number.' sebesar '.$money($order->total).'. Berikut bukti transfernya.') }}" target="_blank" rel="noopener noreferrer" class="shop-no-print mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#1da851]">
                            <x-icon name="whatsapp" class="size-4" /> Kirim bukti transfer via WhatsApp
                        </a>
                    @endif
                </section>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
                {{-- Items --}}
                <section class="{{ $ds->card('p-5 sm:p-6', false) }} border border-line">
                    <h2 class="font-heading text-lg font-bold text-ink">Produk ({{ $order->itemsCount() }})</h2>
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($order->items as $item)
                            <li class="flex gap-3 py-3.5">
                                <span class="size-16 shrink-0 overflow-hidden rounded-brand bg-surface-alt"><x-site.img :src="$item->url('image')" :alt="$item->product_name" icon="cube" class="size-full object-cover" /></span>
                                <span class="min-w-0 flex-1 text-sm">
                                    <span class="line-clamp-2 font-semibold text-ink">{{ $item->product_name }}</span>
                                    @if ($item->variant_label)<span class="block text-muted">{{ $item->variant_label }}</span>@endif
                                    <span class="block text-muted">{{ $item->quantity }} × {{ $money($item->price) }}</span>
                                </span>
                                <span class="shrink-0 text-sm font-semibold text-ink">{{ $money($item->subtotal) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="mt-3 space-y-2 border-t border-line pt-4 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-muted">Subtotal</dt><dd class="text-ink">{{ $money($order->subtotal) }}</dd></div>
                        @if ((float) $order->discount > 0)
                            <div class="flex justify-between gap-4"><dt class="text-muted">Diskon {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</dt><dd class="text-emerald-600">-{{ $money($order->discount) }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-4"><dt class="text-muted">Ongkos kirim</dt><dd class="text-ink">{{ (float) $order->shipping_cost > 0 ? $money($order->shipping_cost) : 'Gratis' }}</dd></div>
                        @if ($order->tax_name)
                            <div class="flex justify-between gap-4"><dt class="text-muted">{{ $order->tax_name }}{{ $order->tax_inclusive ? ' (termasuk)' : '' }}</dt><dd class="text-ink">{{ $money($order->tax) }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-4 border-t border-line pt-3 text-base"><dt class="font-semibold text-ink">Total</dt><dd class="font-heading text-xl font-bold text-ink">{{ $money($order->total) }}</dd></div>
                    </dl>
                </section>

                {{-- Customer & delivery --}}
                <aside class="space-y-4">
                    <section class="{{ $ds->card('p-5', false) }} border border-line text-sm">
                        <h3 class="text-xs font-semibold tracking-wide text-muted uppercase">Pemesan</h3>
                        <p class="mt-2 font-semibold text-ink">{{ $order->customer_name }}</p>
                        <p class="break-all text-muted">{{ $order->customer_email }}</p>
                        @if ($order->customer_phone)<p class="text-muted">{{ $order->customer_phone }}</p>@endif
                    </section>
                    <section class="{{ $ds->card('p-5', false) }} border border-line text-sm">
                        <h3 class="text-xs font-semibold tracking-wide text-muted uppercase">Pengiriman</h3>
                        <p class="mt-2 font-semibold text-ink">{{ $order->shipping_method ?: '—' }}</p>
                        @if ($address)
                            <p class="mt-1 text-ink">{{ $address['name'] ?? $order->customer_name }} {{ ! empty($address['phone']) ? '· '.$address['phone'] : '' }}</p>
                            <p class="text-muted">{{ collect([$address['address'] ?? null, $address['city'] ?? null, $address['province'] ?? null, $address['postal_code'] ?? null, $address['country'] ?? null])->filter()->implode(', ') }}</p>
                        @endif
                    </section>
                    <section class="{{ $ds->card('p-5', false) }} border border-line text-sm">
                        <h3 class="text-xs font-semibold tracking-wide text-muted uppercase">Pembayaran</h3>
                        <p class="mt-2 font-semibold text-ink">{{ $order->payment_method ?: '—' }}</p>
                        @if ($order->paid_at)<p class="text-emerald-600">Lunas {{ $order->paid_at->translatedFormat('d M Y') }}</p>@endif
                    </section>
                    @if ($order->notes)
                        <section class="{{ $ds->card('p-5', false) }} border border-line text-sm">
                            <h3 class="text-xs font-semibold tracking-wide text-muted uppercase">Catatan</h3>
                            <p class="mt-2 whitespace-pre-line text-ink">{{ $order->notes }}</p>
                        </section>
                    @endif
                </aside>
            </div>

            <div class="shop-no-print flex flex-wrap gap-3">
                <a href="{{ $site->shop('products') }}" class="{{ $ds->btn('primary') }}">Lanjut Belanja</a>
                @if ($customer)
                    <a href="{{ $site->account('orders') }}" class="{{ $ds->btn('secondary') }}">Semua Pesanan Saya</a>
                @else
                    <a href="{{ $site->shop('order/track') }}" class="{{ $ds->btn('secondary') }}">Lacak Pesanan Lain</a>
                @endif
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}?text={{ rawurlencode('Halo, saya ingin menanyakan pesanan #'.$order->order_number.'.') }}" target="_blank" rel="noopener noreferrer" class="{{ $ds->btn('ghost') }}"><x-icon name="chat" class="size-4" /> Tanya Penjual</a>
                @endif
            </div>
        </div>
    </div>
@endsection
