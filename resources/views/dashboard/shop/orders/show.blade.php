@php
    use App\Models\Shop\Order;
    use App\Services\Shop\OrderService;
    use App\Support\Shop\Money;

    $money = fn ($amount) => Money::format($amount, $order->currency);
    $address = collect($order->shipping_address ?? [])->filter(fn ($v) => is_scalar($v) && filled($v));
    $transitionStyles = ['cancelled' => 'btn-danger', 'refunded' => 'btn-secondary', 'completed' => 'btn-success'];
    $historyTypes = ['order' => 'bg-brand-500', 'payment' => 'bg-emerald-500', 'shipping' => 'bg-sky-500'];
@endphp
<x-website-layout :company="$company" :title="'Order '.$order->order_number">
    @include('dashboard.shop.partials.nav')

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('websites.shop.orders.index', $company) }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="size-4" /> Semua pesanan</a>
            <h2 class="mt-1 flex flex-wrap items-center gap-2 text-xl font-bold text-slate-900">
                {{ $order->order_number }}
                <x-status-badge :status="$order->status" />
                <x-status-badge :status="$order->payment_status" />
            </h2>
            <p class="text-sm text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} · via {{ $order->channel === 'whatsapp' ? 'WhatsApp' : 'Website' }}</p>
        </div>
        <a href="{{ route('websites.shop.orders.invoice', [$company, $order]) }}" target="_blank" class="btn btn-secondary self-start"><x-icon name="printer" class="size-4" /> Invoice</a>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="min-w-0 space-y-6 xl:col-span-2">
            {{-- Items --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Item pesanan ({{ $order->itemsCount() }})</h3></div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <li class="flex gap-4 px-4 py-4 sm:px-6">
                            @if ($item->url('image'))
                                <img src="{{ $item->url('image') }}" alt="" class="size-16 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                            @else
                                <span class="inline-flex size-16 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="photo" class="size-6" /></span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-slate-900">
                                    @if ($item->product)
                                        <a href="{{ route('websites.shop.products.edit', [$company, $item->product]) }}" class="hover:text-brand-600">{{ $item->product_name }}</a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">
                                    @if ($item->variant_label){{ $item->variant_label }} · @endif
                                    SKU {{ $item->sku ?: '—' }}
                                </p>
                                <p class="mt-1 text-sm text-slate-600">{{ $item->quantity }} × {{ $money($item->price) }}
                                    @if (! empty($item->metadata['original_price']) && $item->metadata['original_price'] > $item->price)
                                        <span class="text-xs text-slate-400 line-through">{{ $money($item->metadata['original_price']) }}</span>
                                    @endif
                                </p>
                            </div>
                            <p class="shrink-0 text-sm font-semibold text-slate-900">{{ $money($item->subtotal) }}</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="space-y-2 border-t border-slate-100 px-6 py-4 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ $money($order->subtotal) }}</dd></div>
                    @if ((float) $order->discount > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">Diskon @if ($order->coupon_code)<span class="badge badge-brand ml-1">{{ $order->coupon_code }}</span>@endif</dt><dd class="text-rose-600">−{{ $money($order->discount) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-slate-500">Ongkir @if ($order->shipping_method)<span class="text-xs">({{ $order->shipping_method }})</span>@endif</dt><dd>{{ $money($order->shipping_cost) }}</dd></div>
                    @if ((float) $order->tax > 0)
                        <div class="flex justify-between"><dt class="text-slate-500">{{ $order->tax_name ?: 'Pajak' }} {{ $order->tax_inclusive ? '(termasuk)' : '' }}</dt><dd>{{ $money($order->tax) }}</dd></div>
                    @endif
                    <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>{{ $money($order->total) }}</dd></div>
                </dl>
            </div>

            {{-- Status actions --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">Update status</h3>
                    <p class="text-xs text-slate-500">Status saat ini: <span class="font-medium text-slate-700">{{ $order->statusLabel() }}</span>. Pelanggan menerima notifikasi setiap perubahan.</p>
                </div>
                <div class="card-body space-y-5">
                    @if (empty($transitions))
                        <p class="text-sm text-slate-500">Pesanan ini sudah final — tidak ada perubahan status lagi.</p>
                    @else
                        <form method="POST" action="{{ route('websites.shop.orders.status', [$company, $order]) }}" class="space-y-3">
                            @csrf
                            <input type="text" name="note" maxlength="255" placeholder="Catatan (opsional), mis. alasan pembatalan" class="form-input">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($transitions as $to)
                                    <button type="submit" class="btn {{ $transitionStyles[$to] ?? 'btn-primary' }} btn-sm" name="status" value="{{ $to }}"
                                            @if (in_array($to, ['cancelled', 'refunded'], true)) onclick="return confirm(@js('Ubah status menjadi '.(Order::STATUSES[$to] ?? $to).'? Stok akan dikembalikan.'))" @endif>
                                        → {{ OrderService::LABELS[$to] ?? (Order::STATUSES[$to] ?? $to) }}
                                    </button>
                                @endforeach
                            </div>
                            @error('status')<p class="form-error">{{ $message }}</p>@enderror
                        </form>
                    @endif

                    @if ($order->payment_status !== 'paid' && ! in_array($order->status, ['cancelled', 'refunded'], true))
                        <form method="POST" action="{{ route('websites.shop.orders.paid', [$company, $order]) }}" class="flex flex-col gap-2 border-t border-slate-100 pt-5 sm:flex-row">
                            @csrf
                            <input type="text" name="note" maxlength="255" placeholder="Catatan pembayaran (mis. transfer BCA 12/10)" class="form-input flex-1">
                            <button class="btn btn-success"><x-icon name="banknotes" class="size-4" /> Tandai lunas</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('websites.shop.orders.tracking', [$company, $order]) }}" class="grid gap-2 border-t border-slate-100 pt-5 sm:grid-cols-[1fr_1.5fr_auto]">
                        @csrf
                        <input type="text" name="courier" value="{{ old('courier', $order->shipment?->courier) }}" maxlength="60" placeholder="Kurir (JNE, J&T…)" class="form-input">
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->shipment?->tracking_number) }}" maxlength="100" required placeholder="Nomor resi" class="form-input">
                        <button class="btn btn-secondary"><x-icon name="truck" class="size-4" /> Simpan resi</button>
                        @error('tracking_number')<p class="form-error sm:col-span-3">{{ $message }}</p>@enderror
                    </form>
                </div>
            </div>

            {{-- History --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Riwayat & catatan</h3></div>
                <ol class="card-body space-y-4">
                    @forelse ($order->histories->reverse() as $history)
                        <li class="flex gap-3">
                            <span class="mt-1.5 size-2.5 shrink-0 rounded-full {{ $historyTypes[$history->type] ?? 'bg-slate-400' }}"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $history->note ?: (OrderService::LABELS[$history->status] ?? $history->status) }}</p>
                                <p class="text-xs text-slate-400">{{ $history->created_at?->format('d M Y H:i') }} · {{ ucfirst($history->type) }}{{ $history->user ? ' · oleh '.$history->user->name : '' }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Belum ada riwayat.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="min-w-0 space-y-6">
            {{-- Timeline --}}
            <div class="card card-body">
                <h3 class="mb-4 text-sm font-semibold text-slate-900">Progres pesanan</h3>
                <ol class="relative space-y-5 border-l-2 border-slate-100 pl-5">
                    @foreach ($timeline as $step)
                        <li class="relative">
                            <span class="absolute top-0.5 -left-[27px] inline-flex size-4 items-center justify-center rounded-full ring-4 ring-white {{ $step['done'] ? (! empty($step['danger']) ? 'bg-rose-500' : 'bg-emerald-500') : 'bg-slate-200' }}">
                                @if ($step['done'])<x-icon name="check" class="size-2.5 text-white" stroke="3" />@endif
                            </span>
                            <p class="text-sm font-medium {{ $step['done'] ? 'text-slate-900' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                            @if ($step['at'])<p class="text-xs text-slate-400">{{ $step['at']->format('d M Y H:i') }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Customer --}}
            <div class="card card-body space-y-3 text-sm">
                <h3 class="text-sm font-semibold text-slate-900">Pelanggan</h3>
                <div>
                    <p class="font-medium text-slate-900">{{ $order->customer_name }}</p>
                    @if ($order->customer_email)<a href="mailto:{{ $order->customer_email }}" class="block text-brand-600 hover:underline">{{ $order->customer_email }}</a>@endif
                    @if ($order->customer_phone)<p class="text-slate-600">{{ $order->customer_phone }}</p>@endif
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($wa = $order->customer_whatsapp ?: $order->customer_phone)
                        @php($waNumber = preg_replace('/^0/', '62', preg_replace('/\D+/', '', $wa)))
                        <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo '.$order->customer_name.', terkait pesanan '.$order->order_number.' di '.$company->name) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><x-icon name="whatsapp" class="size-3.5 text-emerald-600" /> WhatsApp</a>
                    @endif
                    @if ($order->customer)
                        <a href="{{ route('websites.shop.customers.show', [$company, $order->customer]) }}" class="btn btn-ghost btn-sm">Profil pelanggan →</a>
                    @endif
                </div>
            </div>

            {{-- Shipping --}}
            <div class="card card-body space-y-2 text-sm">
                <h3 class="text-sm font-semibold text-slate-900">Pengiriman</h3>
                <p class="text-slate-700">{{ $order->shipping_method ?: '—' }} <x-status-badge :status="$order->shipping_status ?? 'pending'" class="ml-1" /></p>
                @if ($address->isNotEmpty())
                    <address class="not-italic text-slate-600">
                        @foreach (['name', 'phone', 'address', 'city', 'province', 'postal_code', 'country'] as $key)
                            @if ($address->has($key))<span class="block">{{ $address[$key] }}</span>@endif
                        @endforeach
                    </address>
                @else
                    <p class="text-slate-500">Tanpa alamat (ambil di toko / dikonfirmasi via WhatsApp).</p>
                @endif
                @if ($order->shipment?->tracking_number)
                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-slate-700"><span class="text-xs text-slate-500">Resi</span><br><span class="font-semibold">{{ $order->shipment->courier ? $order->shipment->courier.' · ' : '' }}{{ $order->shipment->tracking_number }}</span></p>
                @endif
            </div>

            {{-- Payment --}}
            <div class="card card-body space-y-2 text-sm">
                <h3 class="text-sm font-semibold text-slate-900">Pembayaran</h3>
                <p class="text-slate-700">{{ $order->payment_method ?: '—' }} <x-status-badge :status="$order->payment_status" class="ml-1" /></p>
                @if ($order->paid_at)<p class="text-xs text-slate-500">Lunas {{ $order->paid_at->format('d M Y H:i') }}</p>@endif
                @if (is_array($order->payment?->instructions))
                    <dl class="space-y-1 rounded-lg bg-slate-50 px-3 py-2 text-xs">
                        @foreach ($order->payment->instructions as $key => $value)
                            @if (is_scalar($value) && filled($value))
                                <div><dt class="inline text-slate-500">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}:</dt> <dd class="inline text-slate-700">{{ $value }}</dd></div>
                            @endif
                        @endforeach
                    </dl>
                @endif
            </div>

            @if ($order->notes)
                <div class="card card-body text-sm">
                    <h3 class="text-sm font-semibold text-slate-900">Catatan pelanggan</h3>
                    <p class="mt-2 whitespace-pre-line text-slate-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-website-layout>
