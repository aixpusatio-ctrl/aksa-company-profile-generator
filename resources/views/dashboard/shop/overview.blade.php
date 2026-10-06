@php
    use App\Support\Shop\Money;

    $series = collect($stats['series']);
    $maxRevenue = max(1, (float) $series->max('revenue'));
    $maxOrders = max(1, (int) $series->max('orders'));
    $count = max(1, $series->count() - 1);
    // Revenue line chart in a 600x180 viewBox.
    $points = $series->values()->map(fn ($day, $i) => round($i / $count * 600, 1).','.round(170 - ($day['revenue'] / $maxRevenue) * 155, 1));
    $area = '0,180 '.$points->implode(' ').' 600,180';
    $periodRevenue = (float) $series->sum('revenue');
    $periodOrders = (int) $series->sum('orders');
    $maxTop = max(1, (int) collect($stats['top_products'])->max('quantity'));
@endphp
<x-website-layout :company="$company" title="Online Shop">
    @include('dashboard.shop.partials.nav')

    @unless ($company->hasShop())
        <div class="mb-6 rounded-2xl border border-brand-200 bg-gradient-to-br from-brand-50 to-white p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600 text-white"><x-icon name="shopping-bag" class="size-6" /></span>
                <div class="flex-1">
                    <h2 class="text-base font-semibold text-slate-900">Ubah website ini menjadi toko online</h2>
                    <p class="mt-1 text-sm text-slate-600">Saat diaktifkan, kami menyiapkan tag produk, metode pengiriman (pickup & flat rate) dan pembayaran (transfer bank & COD) default. Anda bisa mengubah semuanya kapan saja.</p>
                </div>
                <form method="POST" action="{{ route('websites.shop.toggle', $company) }}">@csrf<button class="btn btn-primary"><x-icon name="bolt" class="size-4" /> Aktifkan Online Shop</button></form>
            </div>
        </div>
    @endunless

    <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <x-stat-card label="Pendapatan (lunas)" :value="Money::format($stats['revenue'])" icon="banknotes" color="green" />
        <x-stat-card label="Pesanan" :value="number_format($stats['orders'])" icon="receipt" :hint="$stats['pending'].' menunggu diproses · '.$stats['completed'].' selesai'" />
        <x-stat-card label="Produk" :value="number_format($stats['products'])" icon="cube" color="violet" :hint="$stats['published_products'].' dipublikasikan · '.number_format($stats['sold']).' unit terjual'" />
        <x-stat-card label="Pelanggan" :value="number_format($stats['customers'])" icon="users" color="sky" :hint="$stats['low_stock'].' produk stok menipis'" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Penjualan 30 hari terakhir</h2>
                    <p class="text-xs text-slate-500">Total pesanan valid (tidak termasuk dibatalkan/refund).</p>
                </div>
                <div class="text-right">
                    <p class="font-display text-xl font-bold text-slate-900">{{ Money::format($periodRevenue) }}</p>
                    <p class="text-xs text-slate-500">{{ $periodOrders }} pesanan</p>
                </div>
            </div>
            <div class="card-body">
                <svg viewBox="0 0 600 180" class="h-44 w-full" preserveAspectRatio="none" role="img" aria-label="Grafik pendapatan harian">
                    <defs>
                        <linearGradient id="rev-fill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#2546ea" stop-opacity="0.25" />
                            <stop offset="100%" stop-color="#2546ea" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    @foreach ([15, 60, 105, 150] as $y)
                        <line x1="0" x2="600" y1="{{ $y }}" y2="{{ $y }}" stroke="#e2e8f0" stroke-width="1" vector-effect="non-scaling-stroke" />
                    @endforeach
                    <polygon points="{{ $area }}" fill="url(#rev-fill)" />
                    <polyline points="{{ $points->implode(' ') }}" fill="none" stroke="#2546ea" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                </svg>
                {{-- Daily orders bars --}}
                <div class="mt-4 flex h-16 items-end gap-[2px]" aria-label="Jumlah pesanan harian">
                    @foreach ($series as $day)
                        <div class="group relative flex-1 rounded-t bg-brand-200 hover:bg-brand-500" style="height: {{ max(3, round($day['orders'] / $maxOrders * 100)) }}%"
                             title="{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d M') }}: {{ $day['orders'] }} pesanan · {{ Money::format($day['revenue']) }}"></div>
                    @endforeach
                </div>
                <div class="mt-2 flex justify-between text-[11px] text-slate-400">
                    <span>{{ \Illuminate\Support\Carbon::parse($series->first()['date'] ?? now())->format('d M') }}</span>
                    <span>Pesanan / hari (maks {{ $maxOrders }})</span>
                    <span>{{ \Illuminate\Support\Carbon::parse($series->last()['date'] ?? now())->format('d M') }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Produk terlaris</h2>
                <p class="text-xs text-slate-500">Berdasarkan jumlah unit terjual.</p>
            </div>
            <div class="card-body space-y-4">
                @forelse ($stats['top_products'] as $top)
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="truncate font-medium text-slate-800">{{ $top->name }}</span>
                            <span class="shrink-0 text-xs text-slate-500">{{ (int) $top->quantity }} unit</span>
                        </div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width: {{ round($top->quantity / $maxTop * 100) }}%"></div></div>
                        <p class="mt-1 text-[11px] text-slate-400">{{ Money::format($top->revenue) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada penjualan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Pesanan terbaru</h2>
                <a href="{{ route('websites.shop.orders.index', $company) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Semua pesanan →</a>
            </div>
            @if ($recentOrders->isEmpty())
                <div class="p-6"><x-empty-state title="Belum ada pesanan" description="Pesanan dari website akan muncul di sini." icon="receipt" /></div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>Order</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td class="whitespace-nowrap"><a href="{{ route('websites.shop.orders.show', [$company, $order]) }}" class="font-semibold text-brand-600 hover:underline">{{ $order->order_number }}</a><p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</p></td>
                                    <td class="max-w-[12rem] truncate">{{ $order->customer_name }}</td>
                                    <td class="whitespace-nowrap font-medium">{{ Money::format($order->total, $order->currency) }}</td>
                                    <td><x-status-badge :status="$order->status" /></td>
                                    <td><x-status-badge :status="$order->payment_status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Stok menipis</h2>
                    <a href="{{ route('websites.shop.inventory.index', [$company, 'filter' => 'low']) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Inventori →</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($lowStock as $product)
                        <li class="flex items-center justify-between gap-3 px-6 py-3">
                            <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="min-w-0 truncate text-sm font-medium text-slate-800 hover:text-brand-600">{{ $product->name }}</a>
                            <span class="badge {{ $product->availableStock() <= 0 ? 'badge-red' : 'badge-amber' }} shrink-0">{{ $product->availableStock() <= 0 ? 'Habis' : $product->availableStock().' tersisa' }}</span>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-sm text-slate-500">Semua stok aman 👍</li>
                    @endforelse
                </ul>
            </div>
            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><x-icon name="star" class="size-5" /></span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900">{{ $pendingReviews }} ulasan menunggu moderasi</p>
                        <a href="{{ route('websites.shop.reviews.index', $company) }}" class="text-xs font-medium text-brand-600 hover:underline">Moderasi ulasan →</a>
                    </div>
                </div>
            </div>
            <div class="card card-body">
                <p class="text-sm font-semibold text-slate-900">Aksi cepat</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('websites.shop.products.create', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="plus" class="size-3.5" /> Produk</a>
                    <a href="{{ route('websites.shop.coupons.index', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="ticket" class="size-3.5" /> Kupon</a>
                    <a href="{{ route('websites.shop.categories.index', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="tag" class="size-3.5" /> Kategori</a>
                    <a href="{{ route('websites.shop.settings', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="cog" class="size-3.5" /> Pengaturan</a>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
