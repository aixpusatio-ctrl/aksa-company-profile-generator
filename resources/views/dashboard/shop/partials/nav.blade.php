{{-- Online shop sub navigation (tabs) + shop on/off switch + live shop link. --}}
@php
    $tabs = [
        ['websites.shop.overview', 'Overview', 'chart', 'websites.shop.overview'],
        ['websites.shop.products.index', 'Products', 'cube', 'websites.shop.products.*'],
        ['websites.shop.categories.index', 'Categories', 'tag', 'websites.shop.categories.*'],
        ['websites.shop.orders.index', 'Orders', 'receipt', 'websites.shop.orders.*'],
        ['websites.shop.customers.index', 'Customers', 'users', 'websites.shop.customers.*'],
        ['websites.shop.coupons.index', 'Coupons', 'ticket', 'websites.shop.coupons.*'],
        ['websites.shop.inventory.index', 'Inventory', 'archive', 'websites.shop.inventory.*'],
        ['websites.shop.reviews.index', 'Reviews', 'star', 'websites.shop.reviews.*'],
        ['websites.shop.shipping.index', 'Shipping', 'truck', 'websites.shop.shipping.*'],
        ['websites.shop.payments.index', 'Payments', 'credit-card', 'websites.shop.payments.*'],
        ['websites.shop.discounts.index', 'Discounts', 'receipt-percent', 'websites.shop.discounts.*'],
        ['websites.shop.settings', 'Settings', 'cog', 'websites.shop.settings'],
    ];
    $shopUrl = $company->publicUrl().'/shop';
@endphp
<div class="mb-6 space-y-4">
    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl {{ $company->hasShop() ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}"><x-icon name="storefront" class="size-5" /></span>
            <div class="min-w-0">
                <p class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                    {{ $company->shopSetting?->displayName() ?? $company->name }}
                    @if ($company->hasShop())
                        <span class="badge badge-green"><span class="size-1.5 rounded-full bg-current"></span>Toko aktif</span>
                    @else
                        <span class="badge badge-slate"><span class="size-1.5 rounded-full bg-current"></span>Toko nonaktif</span>
                    @endif
                </p>
                <p class="truncate text-xs text-slate-500">
                    @if ($company->hasShop())
                        Menu Shop & Keranjang tampil di website.
                        @unless ($company->isPublished()) <span class="text-amber-600">Website belum dipublish — toko belum bisa diakses publik.</span> @endunless
                    @else
                        Aktifkan untuk menampilkan katalog, keranjang & checkout di website Anda.
                    @endif
                </p>
            </div>
        </div>
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            @if ($company->hasShop())
                <a href="{{ $shopUrl }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><x-icon name="external" class="size-3.5" /> Lihat toko</a>
            @endif
            <form method="POST" action="{{ route('websites.shop.toggle', $company) }}" @if ($company->hasShop()) onsubmit="return confirm('Nonaktifkan online shop? Produk & pesanan tetap tersimpan.')" @endif>
                @csrf
                <button type="submit" role="switch" aria-checked="{{ $company->hasShop() ? 'true' : 'false' }}" class="inline-flex items-center gap-2 rounded-lg px-2 py-1 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <span class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition {{ $company->hasShop() ? 'bg-emerald-500' : 'bg-slate-200' }}">
                        <span class="inline-block size-5 translate-y-0.5 rounded-full bg-white shadow transition {{ $company->hasShop() ? 'translate-x-5.5' : 'translate-x-0.5' }}"></span>
                    </span>
                    {{ $company->hasShop() ? 'ON' : 'OFF' }}
                </button>
            </form>
        </div>
    </div>

    <nav class="-mx-4 flex gap-1 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Menu toko">
        @foreach ($tabs as [$route, $label, $icon, $pattern])
            @php($isActive = request()->routeIs($pattern))
            <a href="{{ route($route, $company) }}" @if ($isActive) aria-current="page" @endif
               class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap transition {{ $isActive ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-slate-900' }}">
                <x-icon :name="$icon" class="size-4 {{ $isActive ? 'text-white' : 'text-slate-400' }}" /> {{ $label }}
            </a>
        @endforeach
    </nav>
</div>
