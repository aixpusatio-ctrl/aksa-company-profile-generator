<x-layouts.app title="Online Shop">
    <x-page-header title="Online Shop" description="Jual produk langsung dari website Anda: katalog, keranjang, checkout, pesanan & stok." />

    @if ($websites->isEmpty())
        <x-empty-state title="Belum ada website" description="Buat website company profile terlebih dahulu, lalu aktifkan online shop di dalamnya." icon="shopping-bag">
            <a href="{{ route('websites.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Buat website</a>
        </x-empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($websites as $website)
                <div class="card flex flex-col">
                    <div class="card-body flex-1">
                        <div class="flex items-start gap-3">
                            @if ($website->url('logo'))
                                <img src="{{ $website->url('logo') }}" alt="" class="size-12 shrink-0 rounded-xl bg-slate-50 object-contain p-1 ring-1 ring-slate-200">
                            @else
                                <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-lg font-bold text-brand-600">{{ mb_strtoupper(mb_substr($website->name, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-slate-900">{{ $website->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $website->primaryHost() }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <x-status-badge :status="$website->status" />
                                    @if ($website->hasShop())
                                        <span class="badge badge-green"><x-icon name="shopping-bag" class="size-3" /> Shop ON</span>
                                    @else
                                        <span class="badge badge-slate">Shop OFF</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($website->hasShop())
                            <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-[11px] text-slate-500">Produk</dt><dd class="text-sm font-bold text-slate-900">{{ $website->shopProducts()->count() }}</dd></div>
                                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-[11px] text-slate-500">Pesanan</dt><dd class="text-sm font-bold text-slate-900">{{ $website->orders()->count() }}</dd></div>
                                <div class="rounded-lg bg-slate-50 p-2"><dt class="text-[11px] text-slate-500">Pending</dt><dd class="text-sm font-bold text-amber-600">{{ $website->orders()->where('status', 'pending')->count() }}</dd></div>
                            </dl>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 px-5 py-3 sm:px-6">
                        @if ($website->hasShop())
                            <a href="{{ route('websites.shop.overview', $website) }}" class="btn btn-primary btn-sm"><x-icon name="chart" class="size-3.5" /> Kelola toko</a>
                            <a href="{{ route('websites.shop.orders.index', $website) }}" class="btn btn-secondary btn-sm"><x-icon name="receipt" class="size-3.5" /> Pesanan</a>
                            <a href="{{ route('websites.shop.products.create', $website) }}" class="btn btn-ghost btn-sm"><x-icon name="plus" class="size-3.5" /> Produk</a>
                        @else
                            <form method="POST" action="{{ route('websites.shop.toggle', $website) }}">
                                @csrf
                                <button class="btn btn-success btn-sm"><x-icon name="bolt" class="size-3.5" /> Aktifkan shop</button>
                            </form>
                            <a href="{{ route('websites.shop.overview', $website) }}" class="btn btn-ghost btn-sm">Lihat detail</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
