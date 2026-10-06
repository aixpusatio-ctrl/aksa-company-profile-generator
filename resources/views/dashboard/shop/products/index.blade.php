@php($threshold = $company->shopSetting?->low_stock_threshold ?? 5)
<x-website-layout :company="$company" title="Produk">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Produk <span class="ml-1 text-sm font-normal text-slate-400">({{ $products->total() }})</span></h2>
                <p class="text-xs text-slate-500">Kelola katalog, harga, stok dan varian.</p>
            </div>
            <a href="{{ route('websites.shop.products.create', $company) }}" class="btn btn-primary btn-sm self-start lg:self-auto"><x-icon name="plus" class="size-4" /> Tambah produk</a>
        </div>

        <form method="GET" class="flex flex-col gap-2 border-b border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:px-6">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau SKU…" class="form-input pl-9">
            </div>
            <select name="status" class="form-input sm:w-40" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (\App\Models\Shop\Product::STATUSES as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="category" class="form-input sm:w-48" onchange="this.form.submit()">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request()->integer('category') === $category->id)>{{ $category->parent_id ? '— ' : '' }}{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary">Filter</button>
        </form>

        @if ($products->isEmpty())
            <div class="p-6">
                <x-empty-state title="{{ $search || $status ? 'Produk tidak ditemukan' : 'Belum ada produk' }}" description="Tambahkan produk pertama Anda — lengkap dengan foto, harga, stok dan varian." icon="cube">
                    <a href="{{ route('websites.shop.products.create', $company) }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Tambah produk</a>
                </x-empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            @php($available = $product->availableStock())
                            <tr>
                                <td class="min-w-[16rem]">
                                    <div class="flex items-center gap-3">
                                        @if ($product->mainImage())
                                            <img src="{{ $product->mainImage() }}" alt="" class="size-12 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                                        @else
                                            <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="photo" class="size-5" /></span>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="block truncate font-semibold text-slate-900 hover:text-brand-600">{{ $product->name }}</a>
                                            <p class="truncate text-xs text-slate-500">
                                                {{ $product->sku ?: 'Tanpa SKU' }}
                                                @if ($product->variants->isNotEmpty()) · {{ $product->variants->count() }} varian @endif
                                                @if ($product->featured) · <span class="text-amber-600">★ Featured</span> @endif
                                            </p>
                                            @if ($product->tags->isNotEmpty())
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach ($product->tags->take(3) as $tag)
                                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">{{ $tag->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap text-slate-500">{{ $product->category?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap">
                                    <span class="font-medium text-slate-900">{{ $product->formattedPrice() }}</span>
                                    @if ($product->isOnSale())
                                        <p class="text-xs text-rose-600">Sale −{{ $product->discountPercent() }}% <span class="text-slate-400 line-through">{{ \App\Support\Shop\Money::format($product->price) }}</span></p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap">
                                    @if (! $product->track_stock)
                                        <span class="badge badge-slate">Tidak dilacak</span>
                                    @elseif ($available <= 0)
                                        <span class="badge {{ $product->stock_status === 'backorder' ? 'badge-violet' : 'badge-red' }}">{{ $product->stock_status === 'backorder' ? 'Pre-order' : 'Habis' }}</span>
                                    @elseif ($product->isLowStock($threshold))
                                        <span class="badge badge-amber">{{ $available }} · menipis</span>
                                    @else
                                        <span class="badge badge-green">{{ $available }} tersedia</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$product->status" /></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit"><x-icon name="pencil" class="size-4" /></a>
                                        <form method="POST" action="{{ route('websites.shop.products.duplicate', [$company, $product]) }}">
                                            @csrf
                                            <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Duplikat"><x-icon name="duplicate" class="size-4" /></button>
                                        </form>
                                        <x-confirm-delete :action="route('websites.shop.products.destroy', [$company, $product])" message="Hapus produk ini? Produk yang pernah dipesan akan diarsipkan." />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $products->links() }}</div>
            @endif
        @endif
    </div>
</x-website-layout>
