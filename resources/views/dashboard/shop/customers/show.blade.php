@php use App\Support\Shop\Money; @endphp
<x-website-layout :company="$company" :title="$customer->name">
    @include('dashboard.shop.partials.nav')

    <a href="{{ route('websites.shop.customers.index', $company) }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="size-4" /> Semua pelanggan</a>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="min-w-0 space-y-6">
            <div class="card card-body">
                <div class="flex items-center gap-4">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xl font-bold text-brand-600">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold text-slate-900">{{ $customer->name }}</p>
                        <span class="badge {{ $customer->isRegistered() ? 'badge-brand' : 'badge-slate' }}">{{ $customer->isRegistered() ? 'Pelanggan terdaftar' : 'Tamu' }}</span>
                    </div>
                </div>
                <dl class="mt-5 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Email</dt><dd class="truncate"><a href="mailto:{{ $customer->email }}" class="text-brand-600 hover:underline">{{ $customer->email }}</a></dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Telepon</dt><dd>{{ $customer->phone ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">WhatsApp</dt><dd>{{ $customer->whatsapp ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Bergabung</dt><dd>{{ $customer->created_at?->format('d M Y') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Login terakhir</dt><dd>{{ $customer->last_login_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                </dl>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-stat-card label="Pesanan" :value="$orders->count()" icon="receipt" />
                <x-stat-card label="Total belanja" :value="Money::format($totalSpent)" icon="banknotes" color="green" class="[&_p.text-3xl]:text-xl" />
            </div>
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-sm font-semibold text-slate-900">Alamat</h3></div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($customer->addresses as $address)
                        <li class="px-6 py-3 text-sm">
                            <p class="font-medium text-slate-800">{{ $address->label ?: $address->name }} @if ($address->is_default)<span class="badge badge-green ml-1">Utama</span>@endif</p>
                            <p class="text-slate-600">{{ collect([$address->address, $address->city, $address->province, $address->postal_code])->filter()->implode(', ') }}</p>
                            @if ($address->phone)<p class="text-xs text-slate-500">{{ $address->phone }}</p>@endif
                        </li>
                    @empty
                        <li class="px-6 py-3 text-sm text-slate-500">Belum ada alamat tersimpan.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="min-w-0 space-y-6 xl:col-span-2">
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-semibold text-slate-900">Riwayat pesanan</h3></div>
                @if ($orders->isEmpty())
                    <p class="px-6 py-4 text-sm text-slate-500">Belum ada pesanan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Order</th><th>Tanggal</th><th>Item</th><th>Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="whitespace-nowrap"><a href="{{ route('websites.shop.orders.show', [$company, $order]) }}" class="font-semibold text-brand-600 hover:underline">{{ $order->order_number }}</a></td>
                                        <td class="text-xs whitespace-nowrap text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                                        <td>{{ $order->items_count }}</td>
                                        <td class="font-medium whitespace-nowrap">{{ Money::format($order->total, $order->currency) }}</td>
                                        <td><x-status-badge :status="$order->status" /></td>
                                        <td><x-status-badge :status="$order->payment_status" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-sm font-semibold text-slate-900">Wishlist ({{ $wishlist->count() }})</h3></div>
                    <ul class="divide-y divide-slate-100">
                        @forelse ($wishlist as $product)
                            <li class="flex items-center justify-between gap-3 px-6 py-3 text-sm">
                                <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="truncate font-medium text-slate-800 hover:text-brand-600">{{ $product->name }}</a>
                                <span class="shrink-0 text-xs text-slate-500">{{ $product->formattedPrice() }}</span>
                            </li>
                        @empty
                            <li class="px-6 py-3 text-sm text-slate-500">Wishlist kosong.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-sm font-semibold text-slate-900">Ulasan ({{ $reviews->count() }})</h3></div>
                    <ul class="divide-y divide-slate-100">
                        @forelse ($reviews as $review)
                            <li class="px-6 py-3 text-sm">
                                <p class="flex items-center justify-between gap-2"><span class="truncate font-medium text-slate-800">{{ $review->product?->name ?? 'Produk dihapus' }}</span><span class="shrink-0 text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - $review->rating) }}</span></span></p>
                                <p class="line-clamp-2 text-slate-600">{{ $review->body }}</p>
                                <x-status-badge :status="$review->status" class="mt-1" />
                            </li>
                        @empty
                            <li class="px-6 py-3 text-sm text-slate-500">Belum ada ulasan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
