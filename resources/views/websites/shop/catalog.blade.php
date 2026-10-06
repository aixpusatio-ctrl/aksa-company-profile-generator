@extends('websites.shop.layout')

@php
    $title = $category?->name ?? ($filters['q'] ? 'Hasil pencarian “'.$filters['q'].'”' : ($filters['sale'] ? 'Produk Promo' : 'Semua Produk'));
    $crumbs = [];
    if ($category?->parent) {
        $crumbs[$category->parent->name] = $site->shop('category/'.$category->parent->slug);
    }
    $crumbs[$category?->name ?? 'Produk'] = null;
    $baseUrl = $category ? $site->shop('category/'.$category->slug) : $site->shop('products');

    // Active filter chips: label => URL without that filter.
    $query = request()->except('page');
    $without = function (string $key, ?string $value = null) use ($query, $baseUrl) {
        $q = $query;
        if ($value === null) {
            unset($q[$key]);
        } elseif (str_contains($key, '.')) {
            [$k, $sub] = explode('.', $key, 2);
            $q[$k][$sub] = array_values(array_diff((array) ($q[$k][$sub] ?? []), [$value]));
        } else {
            $q[$key] = array_values(array_diff((array) ($q[$key] ?? []), [$value]));
        }

        return $baseUrl.($q ? '?'.http_build_query($q) : '');
    };
    $chips = [];
    if ($filters['q']) $chips['“'.$filters['q'].'”'] = $without('q');
    if ($filters['min'] !== null) $chips['Min '.\App\Support\Shop\Money::format($filters['min'])] = $without('min');
    if ($filters['max'] !== null) $chips['Maks '.\App\Support\Shop\Money::format($filters['max'])] = $without('max');
    if ($filters['availability']) $chips['Tersedia'] = $without('availability');
    if ($filters['sale']) $chips['Diskon'] = $without('sale');
    if ($filters['rating']) $chips['Rating '.$filters['rating'].'+'] = $without('rating');
    if ($filters['tag']) $chips['#'.$filters['tag']] = $without('tag');
    foreach ($filters['brand'] as $b) $chips[$b] = $without('brand', $b);
    foreach ($filters['attr'] as $opt => $vals) foreach ($vals as $v) $chips[$opt.': '.$v] = $without('attr.'.$opt, $v);
@endphp

@section('shop')
    @include('websites.shop.partials.page-header', [
        'title' => $title,
        'subtitle' => $category?->description,
        'crumbs' => $crumbs,
    ])

    <div class="{{ $ds->container() }} py-8 sm:py-10" x-data="{ filters: false }">
        @if ($category && $category->children->isNotEmpty())
            <div class="shop-scroll -mx-5 mb-6 flex gap-2 overflow-x-auto px-5 sm:mx-0 sm:flex-wrap sm:px-0">
                @foreach ($category->children as $child)
                    <a href="{{ $site->shop('category/'.$child->slug) }}" class="shrink-0 rounded-full border border-line bg-card px-4 py-1.5 text-sm text-ink transition hover:border-primary hover:text-primary">{{ $child->name }}</a>
                @endforeach
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-[15rem_1fr] lg:gap-10">
            {{-- Sidebar (desktop) --}}
            <aside class="hidden lg:block" aria-label="Filter produk">
                <div class="sticky top-28">
                    @include('websites.shop.partials.filters', ['formId' => 'filters-desktop'])
                </div>
            </aside>

            <div class="min-w-0">
                {{-- Toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-muted">
                        @if ($products->total())
                            Menampilkan <span class="font-semibold text-ink">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span> dari <span class="font-semibold text-ink">{{ $products->total() }}</span> produk
                        @else
                            0 produk
                        @endif
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="filters = true" class="inline-flex h-10 items-center gap-2 rounded-btn border border-line bg-card px-3.5 text-sm font-medium text-ink lg:hidden">
                            <x-shop.icon name="filter" class="size-4" /> Filter
                            @if (count($chips))
                                <span class="inline-flex size-5 items-center justify-center rounded-full bg-primary text-[11px] text-on-primary">{{ count($chips) }}</span>
                            @endif
                        </button>
                        <form method="GET" action="{{ $baseUrl }}" class="flex items-center gap-2">
                            @foreach (request()->except(['sort', 'page']) as $key => $value)
                                @if (is_array($value))
                                    @foreach (\Illuminate\Support\Arr::dot([$key => $value]) as $dotKey => $v)
                                        @php($parts = explode('.', $dotKey))
                                        <input type="hidden" name="{{ array_shift($parts) }}{{ collect($parts)->map(fn ($p) => is_numeric($p) ? '[]' : '['.$p.']')->implode('') }}" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label for="sort" class="sr-only">Urutkan</label>
                            <select id="sort" name="sort" onchange="this.form.submit()" class="h-10 max-w-[11rem] rounded-btn border border-line bg-card px-3 pr-8 text-sm text-ink focus:border-primary focus:outline-none sm:max-w-none">
                                @foreach ($sorts as $value => $label)
                                    <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <noscript><button type="submit" class="{{ $ds->btn('secondary', '!py-2') }}">Urutkan</button></noscript>
                        </form>
                    </div>
                </div>

                {{-- Active filters --}}
                @if (count($chips))
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach ($chips as $label => $removeUrl)
                            <a href="{{ $removeUrl }}" class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary hover:bg-primary/15">
                                {{ $label }} <x-icon name="x" class="size-3" />
                            </a>
                        @endforeach
                        <a href="{{ $baseUrl }}" class="text-xs font-semibold text-muted underline underline-offset-2 hover:text-ink">Hapus semua</a>
                    </div>
                @endif

                <div class="mt-6">
                    @if ($products->isEmpty())
                        @include('websites.shop.partials.empty', [
                            'icon' => 'search',
                            'emptyTitle' => 'Produk tidak ditemukan',
                            'emptyText' => 'Coba kata kunci lain atau kurangi filter yang dipakai.',
                            'actionUrl' => $site->shop('products'),
                            'actionLabel' => 'Lihat semua produk',
                        ])
                    @else
                        @include('websites.shop.partials.grid', ['products' => $products, 'gridCols' => match ($ds->get('product_card')) {
                            'horizontal' => 'grid gap-4 xl:grid-cols-2',
                            'compact' => 'grid grid-cols-2 gap-2.5 sm:grid-cols-3 xl:grid-cols-4',
                            'luxury', 'minimal' => 'grid grid-cols-2 gap-x-4 gap-y-10 sm:grid-cols-3',
                            default => 'grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3',
                        }])
                        @include('websites.shop.partials.pagination', ['paginator' => $products])
                    @endif
                </div>
            </div>
        </div>

        {{-- Mobile filter drawer --}}
        <div x-cloak x-show="filters" class="fixed inset-0 z-[70] lg:hidden" role="dialog" aria-modal="true" aria-label="Filter" @keydown.escape.window="filters = false">
            <div x-show="filters" x-transition.opacity class="absolute inset-0 bg-black/50" @click="filters = false"></div>
            <div x-show="filters" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                 class="absolute inset-y-0 left-0 flex w-[88%] max-w-sm flex-col bg-surface text-ink shadow-2xl">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="font-heading text-lg font-bold">Filter</h2>
                    <button type="button" @click="filters = false" class="inline-flex size-9 items-center justify-center rounded-full hover:bg-surface-alt" aria-label="Tutup"><x-icon name="x" class="size-5" /></button>
                </div>
                <div class="flex-1 overflow-y-auto px-5 py-5">
                    @include('websites.shop.partials.filters', ['formId' => 'filters-mobile'])
                </div>
            </div>
        </div>
    </div>
@endsection
