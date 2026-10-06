@php
    use App\Models\Shop\Order;
    use App\Support\Shop\Money;
    $cur = $order->currency;
    $address = collect($order->shipping_address ?? [])->filter(fn ($v) => filled($v));
    $historyLabels = ['order' => Order::STATUSES, 'payment' => Order::PAYMENT_STATUSES, 'shipping' => Order::SHIPPING_STATUSES];
    $typeLabels = ['order' => 'Pesanan', 'payment' => 'Pembayaran', 'shipping' => 'Pengiriman'];
@endphp
<x-layouts.admin :title="'Order '.$order->order_number">
    <x-page-header :title="'Order '.$order->order_number" :back="route('admin.shop.orders')"
        :description="($order->companyProfile?->name ?? 'Toko dihapus').' · '.$order->created_at?->format('d M Y H:i')">
        <x-slot:actions>
            @include('admin.shop.partials.badge', ['type' => 'order', 'value' => $order->status])
            @include('admin.shop.partials.badge', ['type' => 'payment', 'value' => $order->payment_status])
            @include('admin.shop.partials.badge', ['type' => 'shipping', 'value' => $order->shipping_status])
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
        <x-icon name="info" class="mt-0.5 size-4 shrink-0" />
        <p>Tampilan baca-saja. Pesanan dikelola oleh penjual melalui dashboard toko mereka.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Items (snapshot at checkout time) --}}
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Item Pesanan</h2>
                    <p class="text-sm text-slate-500">Snapshot produk saat checkout.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->items as $item)
                                @php($image = $item->url('image'))
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            @if ($image)
                                                <img src="{{ $image }}" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-slate-200" loading="lazy">
                                            @else
                                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="cube" class="size-5" /></span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ collect([$item->variant_label, $item->sku ? 'SKU '.$item->sku : null])->filter()->implode(' · ') ?: '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right whitespace-nowrap text-slate-600">{{ Money::format($item->price, $cur) }}</td>
                                    <td class="text-right whitespace-nowrap text-slate-600">{{ $item->quantity }}</td>
                                    <td class="text-right font-medium whitespace-nowrap text-slate-900">{{ Money::format($item->subtotal, $cur) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <dl class="space-y-2 border-t border-slate-100 px-6 py-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="text-slate-900">{{ Money::format($order->subtotal, $cur) }}</dd></div>
                    @if ((float) $order->discount > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">Diskon @if ($order->coupon_code)<span class="badge badge-brand ml-1">{{ $order->coupon_code }}</span>@endif</dt><dd class="text-emerald-600">−{{ Money::format($order->discount, $cur) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-slate-500">Ongkos kirim</dt><dd class="text-slate-900">{{ Money::format($order->shipping_cost, $cur) }}</dd></div>
                    @if ((float) $order->tax > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">{{ $order->tax_name ?: 'Pajak' }}{{ $order->tax_inclusive ? ' (termasuk)' : '' }}</dt><dd class="text-slate-900">{{ Money::format($order->tax, $cur) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-semibold"><dt class="text-slate-900">Total</dt><dd class="text-slate-900">{{ Money::format($order->total, $cur) }}</dd></div>
                </dl>
            </div>

            {{-- Timeline --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Riwayat</h2>
                </div>
                @if ($order->histories->isEmpty())
                    <p class="px-6 py-6 text-sm text-slate-500">Belum ada riwayat status.</p>
                @else
                    <ol class="space-y-5 px-6 py-5">
                        @foreach ($order->histories as $history)
                            <li class="relative flex gap-3">
                                <span class="mt-1.5 size-2.5 shrink-0 rounded-full bg-brand-500"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-slate-900">
                                        {{ $typeLabels[$history->type] ?? ucfirst($history->type) }}:
                                        {{ $historyLabels[$history->type][$history->status] ?? ucfirst(str_replace('_', ' ', $history->status)) }}
                                    </p>
                                    @if ($history->note)
                                        <p class="text-sm text-slate-600">{{ $history->note }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400">{{ $history->created_at?->format('d M Y H:i') }} · {{ $history->user?->name ?? 'Sistem / pelanggan' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            @if ($order->notes)
                <div class="card card-body">
                    <h2 class="font-display text-base font-semibold text-slate-900">Catatan Pelanggan</h2>
                    <p class="mt-2 text-sm whitespace-pre-line text-slate-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Shop --}}
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Toko</h2>
                @if ($order->companyProfile)
                    <a href="{{ route('admin.companies.show', $order->companyProfile) }}" class="mt-2 block text-sm font-medium text-brand-600 hover:text-brand-700">{{ $order->companyProfile->name }}</a>
                    <p class="text-xs text-slate-500">{{ $order->companyProfile->subdomainHost() }}</p>
                @else
                    <p class="mt-2 text-sm text-slate-400">—</p>
                @endif
                <p class="mt-2 text-xs text-slate-500">Kanal: {{ $order->channel === 'whatsapp' ? 'WhatsApp' : 'Web' }}</p>
            </div>

            {{-- Customer --}}
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Pelanggan</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-xs text-slate-500">Nama</dt><dd class="text-slate-900">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Email</dt><dd class="break-all text-slate-900">{{ $order->customer_email }}</dd></div>
                    @if ($order->customer_phone)
                        <div><dt class="text-xs text-slate-500">Telepon</dt><dd class="text-slate-900">{{ $order->customer_phone }}</dd></div>
                    @endif
                    @if ($order->customer_whatsapp)
                        <div><dt class="text-xs text-slate-500">WhatsApp</dt><dd class="text-slate-900">{{ $order->customer_whatsapp }}</dd></div>
                    @endif
                    <div><dt class="text-xs text-slate-500">Akun</dt><dd class="text-slate-900">{{ $order->customer_id ? 'Pelanggan #'.$order->customer_id : 'Tamu' }}</dd></div>
                </dl>
            </div>

            {{-- Shipping address --}}
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Alamat Pengiriman</h2>
                @if ($address->isEmpty())
                    <p class="mt-2 text-sm text-slate-500">Tidak ada alamat (tidak memerlukan pengiriman / dikonfirmasi via WhatsApp).</p>
                @else
                    <div class="mt-2 space-y-0.5 text-sm text-slate-700">
                        @if ($address->get('name'))<p class="font-medium text-slate-900">{{ $address->get('name') }}</p>@endif
                        @if ($address->get('phone'))<p>{{ $address->get('phone') }}</p>@endif
                        @if ($address->get('address'))<p class="whitespace-pre-line">{{ $address->get('address') }}</p>@endif
                        <p>{{ $address->only(['city', 'province', 'postal_code'])->implode(', ') }}</p>
                        @if ($address->get('country'))<p>{{ $address->get('country') }}</p>@endif
                    </div>
                @endif
            </div>

            {{-- Payment --}}
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Pembayaran</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-xs text-slate-500">Metode</dt><dd class="text-slate-900">{{ $order->payment_method ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Status</dt><dd>@include('admin.shop.partials.badge', ['type' => 'payment', 'value' => $order->payment_status])</dd></div>
                    @if ($order->payment)
                        <div><dt class="text-xs text-slate-500">Provider</dt><dd class="text-slate-900">{{ $order->payment->provider }} · {{ $order->payment->method }}</dd></div>
                        <div><dt class="text-xs text-slate-500">Jumlah</dt><dd class="text-slate-900">{{ Money::format($order->payment->amount, $cur) }}</dd></div>
                        @if ($order->payment->reference)
                            <div><dt class="text-xs text-slate-500">Referensi</dt><dd class="break-all text-slate-900">{{ $order->payment->reference }}</dd></div>
                        @endif
                    @endif
                    @if ($order->paid_at)
                        <div><dt class="text-xs text-slate-500">Dibayar</dt><dd class="text-slate-900">{{ $order->paid_at->format('d M Y H:i') }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Shipment --}}
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Pengiriman</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-xs text-slate-500">Metode</dt><dd class="text-slate-900">{{ $order->shipping_method ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Status</dt><dd>@include('admin.shop.partials.badge', ['type' => 'shipping', 'value' => $order->shipping_status])</dd></div>
                    @if ($order->shipment)
                        @if ($order->shipment->courier)
                            <div><dt class="text-xs text-slate-500">Kurir</dt><dd class="text-slate-900">{{ $order->shipment->courier }}</dd></div>
                        @endif
                        @if ($order->shipment->tracking_number)
                            <div><dt class="text-xs text-slate-500">No. Resi</dt><dd class="font-mono break-all text-slate-900">{{ $order->shipment->tracking_number }}</dd></div>
                        @endif
                        @if ($order->shipment->delivered_at)
                            <div><dt class="text-xs text-slate-500">Diterima</dt><dd class="text-slate-900">{{ $order->shipment->delivered_at->format('d M Y H:i') }}</dd></div>
                        @endif
                    @endif
                    @if ($order->shipped_at)
                        <div><dt class="text-xs text-slate-500">Dikirim</dt><dd class="text-slate-900">{{ $order->shipped_at->format('d M Y H:i') }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</x-layouts.admin>
