@php
    use App\Support\Shop\Money;

    $dt = fn ($v) => $v ? $v->format('Y-m-d\TH:i') : null;
    $onSale = request()->boolean('on_sale');
@endphp
<x-website-layout :company="$company" title="Diskon & Pajak">
    @include('dashboard.shop.partials.nav')

    <div class="grid gap-6 2xl:grid-cols-3">
        <div class="card min-w-0 2xl:col-span-2">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Harga promo produk</h2>
                    <p class="text-xs text-slate-500">Atur harga promo & jadwalnya. Kosongkan harga promo untuk menghapus diskon.</p>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('websites.shop.discounts.index', $company) }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ ! $onSale ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Semua</a>
                    <a href="{{ route('websites.shop.discounts.index', [$company, 'on_sale' => 1]) }}" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $onSale ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Sedang promo</a>
                </div>
            </div>
            @if ($products->isEmpty())
                <div class="p-6"><x-empty-state title="Tidak ada produk" description="Produk aktif akan tampil di sini untuk diberi harga promo." icon="receipt-percent" /></div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($products as $product)
                        <li class="px-4 py-4 sm:px-6">
                            <form method="POST" action="{{ route('websites.shop.discounts.update', [$company, $product]) }}" class="grid gap-3 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                                @csrf
                                @method('PUT')
                                <div class="flex min-w-0 items-center gap-3">
                                    @if ($product->mainImage())
                                        <img src="{{ $product->mainImage() }}" alt="" class="size-10 shrink-0 rounded-lg object-cover">
                                    @else
                                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="cube" class="size-4" /></span>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('websites.shop.products.edit', [$company, $product]) }}" class="block truncate text-sm font-semibold text-slate-900 hover:text-brand-600">{{ $product->name }}</a>
                                        <p class="text-xs text-slate-500">Normal {{ Money::format($product->price) }}
                                            @if ($product->isOnSale())<span class="badge badge-red ml-1">−{{ $product->discountPercent() }}% aktif</span>
                                            @elseif ($product->sale_price !== null)<span class="badge badge-amber ml-1">Terjadwal</span>@endif
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Harga promo</label>
                                    <input type="number" name="sale_price" step="any" min="0" value="{{ $product->sale_price !== null ? (float) $product->sale_price : '' }}" class="form-input py-1.5 text-sm">
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Mulai</label>
                                    <input type="datetime-local" name="sale_starts_at" value="{{ $dt($product->sale_starts_at) }}" class="form-input py-1.5 text-sm">
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-slate-500">Berakhir</label>
                                    <input type="datetime-local" name="sale_ends_at" value="{{ $dt($product->sale_ends_at) }}" class="form-input py-1.5 text-sm">
                                </div>
                                <button class="btn btn-secondary btn-sm">Simpan</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                @if ($products->hasPages())
                    <div class="border-t border-slate-100 px-6 py-4">{{ $products->links() }}</div>
                @endif
            @endif
            <p class="border-t border-slate-100 px-6 py-3 text-xs text-slate-500">Butuh kode promo? Buat di <a href="{{ route('websites.shop.coupons.index', $company) }}" class="font-medium text-brand-600 hover:underline">Kupon</a>.</p>
        </div>

        <div class="card self-start">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Pajak</h2>
                    <p class="text-xs text-slate-500">Hanya satu pajak aktif yang diterapkan di checkout.</p>
                </div>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($taxes as $tax)
                    <li class="px-6 py-4" x-data="{ edit: false }">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $tax->name }} · {{ rtrim(rtrim(number_format((float) $tax->rate, 2, ',', '.'), '0'), ',') }}%</p>
                                <p class="text-xs text-slate-500">{{ $tax->inclusive ? 'Sudah termasuk dalam harga' : 'Ditambahkan saat checkout' }}</p>
                            </div>
                            @if ($tax->is_active)<span class="badge badge-green">Aktif</span>@else<span class="badge badge-slate">Nonaktif</span>@endif
                            <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="edit = !edit" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                            <x-confirm-delete :action="route('websites.shop.taxes.destroy', [$company, $tax])" message="Hapus pajak ini?" />
                        </div>
                        <form x-cloak x-show="edit" x-collapse method="POST" action="{{ route('websites.shop.taxes.update', [$company, $tax]) }}" class="mt-3 space-y-3">
                            @csrf
                            @method('PUT')
                            @include('dashboard.shop.discounts.partials.tax-fields', ['tax' => $tax])
                            <button class="btn btn-primary btn-sm">Simpan</button>
                        </form>
                    </li>
                @empty
                    <li class="px-6 py-4 text-sm text-slate-500">Belum ada pajak. Harga dijual tanpa pajak.</li>
                @endforelse
            </ul>
            <form method="POST" action="{{ route('websites.shop.taxes.store', $company) }}" class="space-y-3 border-t border-slate-100 px-6 py-4">
                @csrf
                <p class="text-sm font-semibold text-slate-900">Tambah pajak</p>
                @include('dashboard.shop.discounts.partials.tax-fields', ['tax' => null])
                <button class="btn btn-secondary btn-sm"><x-icon name="plus" class="size-3.5" /> Tambah pajak</button>
            </form>
        </div>
    </div>
</x-website-layout>
