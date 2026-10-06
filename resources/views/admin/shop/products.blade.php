@php use App\Models\Shop\Product; use App\Support\Shop\Money; @endphp
<x-layouts.admin title="Products">
    <x-page-header title="Products" description="Semua produk dari seluruh toko online di platform." />

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama produk atau SKU..." class="form-input pl-9">
        </div>
        @include('admin.shop.partials.shop-filter')
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua status</option>
            @foreach (Product::STATUSES as $s)
                <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $status || $shop)
                <a href="{{ route('admin.shop.products') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($products->isEmpty())
        <x-empty-state icon="cube" title="Tidak ada produk" description="Belum ada produk yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Toko</th>
                            <th>Kategori</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Stok</th>
                            <th class="text-right">Terjual</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            @php($image = $product->images->first()?->url('image'))
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($image)
                                            <img src="{{ $image }}" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-slate-200" loading="lazy">
                                        @else
                                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="cube" class="size-5" /></span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-900">{{ $product->name }}</p>
                                            <p class="text-xs text-slate-500">
                                                {{ $product->sku ?: 'Tanpa SKU' }}
                                                @if ($product->featured) · <span class="text-amber-600">Unggulan</span>@endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($product->companyProfile)
                                        <a href="{{ route('admin.companies.show', $product->companyProfile) }}" class="text-slate-700 hover:text-brand-600">{{ $product->companyProfile->name }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-slate-600">{{ $product->category?->name ?? '—' }}</td>
                                <td class="text-right whitespace-nowrap">
                                    <span class="font-medium text-slate-900">{{ Money::format($product->currentPrice(), $product->companyProfile?->shopSetting?->currency) }}</span>
                                    @if ($product->originalPrice())
                                        <p class="text-xs text-slate-400 line-through">{{ Money::format($product->originalPrice(), $product->companyProfile?->shopSetting?->currency) }}</p>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    @if ($product->track_stock)
                                        <span class="{{ $product->stock <= 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ number_format($product->stock) }}</span>
                                    @else
                                        <span class="text-slate-400">∞</span>
                                    @endif
                                    <p class="text-xs text-slate-500">{{ Product::STOCK_STATUSES[$product->stock_status] ?? $product->stock_status }}</p>
                                </td>
                                <td class="text-right whitespace-nowrap text-slate-600">{{ number_format($product->sold_count) }}</td>
                                <td>@include('admin.shop.partials.badge', ['type' => 'product', 'value' => $product->status])</td>
                                <td class="whitespace-nowrap text-slate-500">{{ $product->created_at?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
</x-layouts.admin>
