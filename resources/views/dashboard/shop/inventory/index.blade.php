@php
    $types = \App\Models\Shop\InventoryMovement::TYPES;
    $filters = ['' => 'Semua', 'low' => 'Stok menipis', 'out' => 'Habis'];
@endphp
<x-website-layout :company="$company" title="Inventori">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Stok produk</h2>
                <p class="text-xs text-slate-500">Tersedia = stok fisik − dipesan (reserved). Batas menipis default: {{ $threshold }}.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <div class="flex gap-1">
                    @foreach ($filters as $key => $label)
                        <a href="{{ route('websites.shop.inventory.index', array_filter([$company, 'filter' => $key, 'q' => request('q')])) }}" class="rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap {{ $filter === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
                    @endforeach
                </div>
                <form method="GET" class="flex gap-2">
                    @if ($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk / SKU…" class="form-input sm:w-52">
                    <button class="btn btn-secondary">Cari</button>
                </form>
            </div>
        </div>

        @if ($products->isEmpty())
            <div class="p-6"><x-empty-state title="Tidak ada produk" description="Hanya produk dengan 'Lacak stok' aktif yang tampil di sini." icon="archive" /></div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Produk / varian</th><th>SKU</th><th class="text-right">Stok</th><th class="text-right">Dipesan</th><th class="text-right">Tersedia</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            @php($available = $product->availableStock())
                            @php($limit = $product->low_stock_threshold ?? $threshold)
                            <tr>
                                <td class="min-w-[14rem]">
                                    <div class="flex items-center gap-3">
                                        @if ($product->mainImage())
                                            <img src="{{ $product->mainImage() }}" alt="" class="size-10 shrink-0 rounded-lg object-cover">
                                        @else
                                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="cube" class="size-4" /></span>
                                        @endif
                                        <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="font-semibold text-slate-900 hover:text-brand-600">{{ $product->name }}</a>
                                    </div>
                                </td>
                                <td class="text-xs text-slate-500">{{ $product->sku ?: '—' }}</td>
                                <td class="text-right">{{ $product->hasVariants() ? $product->variants->sum('stock') : $product->stock }}</td>
                                <td class="text-right text-slate-500">{{ $product->hasVariants() ? $product->variants->sum('reserved_stock') : $product->reserved_stock }}</td>
                                <td class="text-right font-semibold">{{ $available }}</td>
                                <td>
                                    @if ($available <= 0)
                                        <span class="badge badge-red">Habis</span>
                                    @elseif ($available <= $limit)
                                        <span class="badge badge-amber">Menipis</span>
                                    @else
                                        <span class="badge badge-green">Aman</span>
                                    @endif
                                </td>
                                <td class="text-right"><button type="button" class="btn btn-secondary btn-sm" @click="$dispatch('open-modal', 'adjust-{{ $product->id }}')"><x-icon name="refresh" class="size-3.5" /> Sesuaikan</button></td>
                            </tr>
                            @foreach ($product->variants as $variant)
                                <tr class="bg-slate-50/50 text-xs">
                                    <td class="pl-16 text-slate-600 {{ $variant->is_active ? '' : 'line-through opacity-60' }}">└ {{ $variant->label }}</td>
                                    <td class="text-slate-500">{{ $variant->sku ?: '—' }}</td>
                                    <td class="text-right">{{ $variant->stock }}</td>
                                    <td class="text-right text-slate-500">{{ $variant->reserved_stock }}</td>
                                    <td class="text-right font-semibold">{{ $variant->availableStock() }}</td>
                                    <td>@if ($variant->availableStock() <= 0)<span class="badge badge-red">Habis</span>@elseif ($variant->availableStock() <= $limit)<span class="badge badge-amber">Menipis</span>@endif</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card mt-6">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Riwayat pergerakan stok</h2>
            <p class="text-xs text-slate-500">30 aktivitas terakhir: penyesuaian, reservasi, penjualan dan retur.</p>
        </div>
        @if ($movements->isEmpty())
            <p class="px-6 py-4 text-sm text-slate-500">Belum ada pergerakan stok.</p>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Waktu</th><th>Produk</th><th>Tipe</th><th class="text-right">Qty</th><th class="text-right">Stok akhir</th><th>Keterangan</th><th>Oleh</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($movements as $movement)
                            <tr class="text-xs">
                                <td class="whitespace-nowrap text-slate-500">{{ $movement->created_at?->format('d M H:i') }}</td>
                                <td class="max-w-[14rem]"><span class="block truncate font-medium text-slate-800">{{ $movement->product?->name ?? '—' }}</span>@if ($movement->variant)<span class="text-slate-500">{{ $movement->variant->label }}</span>@endif</td>
                                <td><span class="badge {{ ['adjustment' => 'badge-blue', 'reserve' => 'badge-amber', 'release' => 'badge-slate', 'sale' => 'badge-green', 'return' => 'badge-violet'][$movement->type] ?? 'badge-slate' }}">{{ $types[$movement->type] ?? $movement->type }}</span></td>
                                <td class="text-right font-semibold {{ $movement->quantity < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                                <td class="text-right">{{ $movement->stock_after ?? '—' }}</td>
                                <td class="max-w-[16rem] truncate text-slate-600">{{ $movement->reason }} @if ($movement->reference)<span class="text-slate-400">· {{ $movement->reference }}</span>@endif</td>
                                <td class="whitespace-nowrap text-slate-500">{{ $movement->user?->name ?? 'Sistem' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @foreach ($products as $product)
        <x-modal :name="'adjust-'.$product->id" :title="'Sesuaikan stok: '.$product->name">
            <form method="POST" action="{{ route('websites.shop.inventory.adjust', [$company, $product]) }}" class="space-y-4">
                @csrf
                @if ($product->variants->isNotEmpty())
                    <div>
                        <label class="form-label">Varian</label>
                        <select name="variant_id" class="form-input" required>
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->label }} (stok {{ $variant->stock }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Arah</label>
                        <select name="direction" class="form-input">
                            <option value="add">+ Tambah stok</option>
                            <option value="remove">− Kurangi stok</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="quantity" min="1" value="1" required class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Alasan</label>
                    <input type="text" name="reason" required maxlength="255" placeholder="Restock supplier, barang rusak, stock opname…" class="form-input">
                </div>
                <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </x-modal>
    @endforeach
</x-website-layout>
