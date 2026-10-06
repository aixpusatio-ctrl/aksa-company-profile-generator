@php use App\Support\Shop\Money; @endphp
<x-layouts.admin title="Shops">
    <x-page-header title="Shops" description="Semua toko online di platform beserta produk, pesanan dan omzet." />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="Toko Aktif" :value="number_format($totals['shops'])" icon="building" color="brand" />
        <x-stat-card label="Produk" :value="number_format($totals['products'])" icon="cube" color="violet" />
        <x-stat-card label="Pesanan" :value="number_format($totals['orders'])" icon="list" color="sky" />
        <x-stat-card label="Pelanggan" :value="number_format($totals['customers'])" icon="users" color="amber" />
        <x-stat-card label="GMV" :value="Money::format($totals['gmv'])" icon="banknotes" color="green" hint="Pesanan lunas, tidak dibatalkan" />
    </div>

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama perusahaan..." class="form-input pl-9">
        </div>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search)
                <a href="{{ route('admin.shop.shops') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($shops->isEmpty())
        <x-empty-state icon="building" title="Belum ada toko" description="Belum ada website yang mengaktifkan modul toko online." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Toko</th>
                            <th>Pemilik</th>
                            <th>Status</th>
                            <th class="text-right">Produk</th>
                            <th class="text-right">Pesanan</th>
                            <th class="text-right">Pelanggan</th>
                            <th class="text-right">Omzet</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($shops as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.companies.show', $company) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $company->name }}</a>
                                    @if ($company->shopSetting?->name && $company->shopSetting->name !== $company->name)
                                        <p class="text-xs text-slate-500">{{ $company->shopSetting->name }}</p>
                                    @endif
                                    <p class="text-xs text-slate-500">{{ $company->subdomainHost() }}</p>
                                </td>
                                <td>
                                    @if ($company->user)
                                        <a href="{{ route('admin.users.show', $company->user) }}" class="text-slate-700 hover:text-brand-600">{{ $company->user->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $company->user->email }}</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-col items-start gap-1">
                                        @if ($company->hasShop())
                                            <span class="badge badge-green"><span class="size-1.5 rounded-full bg-current"></span>Shop on</span>
                                        @else
                                            <span class="badge badge-slate"><span class="size-1.5 rounded-full bg-current"></span>Shop off</span>
                                        @endif
                                        <x-status-badge :status="$company->status" />
                                    </div>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.shop.products', ['shop' => $company->id]) }}" class="text-slate-700 hover:text-brand-600">{{ number_format($company->shop_products_count) }}</a>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.shop.orders', ['shop' => $company->id]) }}" class="text-slate-700 hover:text-brand-600">{{ number_format($company->orders_count) }}</a>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.shop.customers', ['shop' => $company->id]) }}" class="text-slate-700 hover:text-brand-600">{{ number_format($company->customers_count) }}</a>
                                </td>
                                <td class="text-right font-medium whitespace-nowrap text-slate-900">{{ Money::format($company->revenue ?? 0, $company->shopSetting?->currency) }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Detail website"><x-icon name="eye" class="size-4" /></a>
                                        @if ($company->isPublished() && $company->hasShop())
                                            <a href="{{ $company->publicUrl() }}/shop" target="_blank" rel="noopener" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Buka toko"><x-icon name="external" class="size-4" /></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $shops->links() }}</div>
    @endif
</x-layouts.admin>
