@php
    use App\Support\Shop\Money;

    $money = fn ($amount) => Money::format($amount, $order->currency);
    $settings = $company->shopSetting;
    $address = collect($order->shipping_address ?? [])->only(['name', 'phone', 'address', 'city', 'province', 'postal_code', 'country'])->filter(fn ($v) => is_scalar($v) && filled($v));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->order_number }} · {{ $settings?->displayName() ?? $company->name }}</title>
    <meta name="robots" content="noindex">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .sheet { box-shadow: none !important; border: 0 !important; margin: 0 !important; max-width: none !important; }
        }
        @page { margin: 14mm; }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    <div class="no-print mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 pt-6">
        <a href="{{ route('websites.shop.orders.show', [$company, $order]) }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">← Kembali ke pesanan</a>
        <button type="button" onclick="window.print()" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Cetak / Simpan PDF</button>
    </div>

    <main class="sheet mx-auto my-6 max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
        <header class="flex flex-col gap-6 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                @if ($company->url('logo'))
                    <img src="{{ $company->url('logo') }}" alt="" class="h-12 w-auto max-w-[140px] object-contain">
                @endif
                <div>
                    <p class="text-lg font-bold text-slate-900">{{ $settings?->displayName() ?? $company->name }}</p>
                    @if ($company->fullAddress())<p class="max-w-xs text-xs text-slate-500">{{ $company->fullAddress() }}</p>@endif
                    <p class="text-xs text-slate-500">{{ collect([$company->phone, $company->email])->filter()->implode(' · ') }}</p>
                </div>
            </div>
            <div class="sm:text-right">
                <p class="text-2xl font-extrabold tracking-tight text-slate-900">INVOICE</p>
                <p class="text-sm font-semibold text-slate-700">{{ $order->order_number }}</p>
                <p class="text-xs text-slate-500">Tanggal: {{ $order->created_at->format('d M Y') }}</p>
                <p class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                </p>
            </div>
        </header>

        <section class="grid gap-6 py-6 sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Ditagihkan kepada</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->customer_name }}</p>
                <p class="text-sm text-slate-600">{{ $order->customer_email }}</p>
                <p class="text-sm text-slate-600">{{ $order->customer_phone }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Pengiriman</p>
                <p class="mt-1 text-sm font-medium text-slate-800">{{ $order->shipping_method ?: '—' }}</p>
                @foreach ($address as $line)<p class="text-sm text-slate-600">{{ $line }}</p>@endforeach
                @if ($order->shipment?->tracking_number)<p class="text-sm text-slate-600">Resi: {{ $order->shipment->courier }} {{ $order->shipment->tracking_number }}</p>@endif
            </div>
        </section>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-y border-slate-200 text-left text-xs tracking-wide text-slate-500 uppercase">
                        <th class="py-2 pr-3">Produk</th><th class="px-3 py-2 text-right">Harga</th><th class="px-3 py-2 text-right">Qty</th><th class="py-2 pl-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="py-3 pr-3">
                                <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-500">{{ collect([$item->variant_label, $item->sku ? 'SKU '.$item->sku : null])->filter()->implode(' · ') }}</p>
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">{{ $money($item->price) }}</td>
                            <td class="px-3 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="py-3 pl-3 text-right whitespace-nowrap">{{ $money($item->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end">
            <dl class="w-full max-w-xs space-y-1.5 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ $money($order->subtotal) }}</dd></div>
                @if ((float) $order->discount > 0)
                    <div class="flex justify-between"><dt class="text-slate-500">Diskon {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</dt><dd>−{{ $money($order->discount) }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-slate-500">Ongkir</dt><dd>{{ $money($order->shipping_cost) }}</dd></div>
                @if ((float) $order->tax > 0)
                    <div class="flex justify-between"><dt class="text-slate-500">{{ $order->tax_name ?: 'Pajak' }}{{ $order->tax_inclusive ? ' (termasuk)' : '' }}</dt><dd>{{ $money($order->tax) }}</dd></div>
                @endif
                <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>{{ $money($order->total) }}</dd></div>
            </dl>
        </div>

        <footer class="mt-8 border-t border-slate-200 pt-4 text-xs text-slate-500">
            <p>Metode pembayaran: <span class="font-medium text-slate-700">{{ $order->payment_method ?: '—' }}</span>@if ($order->paid_at) · dibayar {{ $order->paid_at->format('d M Y H:i') }}@endif</p>
            @if ($order->notes)<p class="mt-1">Catatan: {{ $order->notes }}</p>@endif
            <p class="mt-3">Terima kasih telah berbelanja di {{ $settings?->displayName() ?? $company->name }}.</p>
        </footer>
    </main>
</body>
</html>
