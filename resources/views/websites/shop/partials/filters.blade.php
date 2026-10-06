{{--
    Catalog filters (GET form). Used in the desktop sidebar and the mobile
    drawer. Vars: $filters, $facets, $category, $formId.
--}}
@php
    $baseAction = $category ? $site->shop('category/'.$category->slug) : $site->shop('products');
@endphp
<form id="{{ $formId }}" method="GET" action="{{ $baseAction }}" class="space-y-6 text-sm text-ink">
    @if ($filters['q'])
        <input type="hidden" name="q" value="{{ $filters['q'] }}">
    @endif
    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
    @if ($filters['tag'])
        <input type="hidden" name="tag" value="{{ $filters['tag'] }}">
    @endif

    {{-- Categories --}}
    @if ($categoriesTree->isNotEmpty())
        <fieldset>
            <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">Kategori</legend>
            <ul class="space-y-0.5">
                <li><a href="{{ $site->shop('products') }}" class="flex justify-between rounded-md px-2 py-1.5 {{ ! $category ? 'bg-primary/10 font-semibold text-primary' : 'hover:bg-surface-alt' }}">Semua produk</a></li>
                @foreach ($categoriesTree as $root)
                    <li>
                        <a href="{{ $site->shop('category/'.$root->slug) }}" class="flex justify-between gap-2 rounded-md px-2 py-1.5 {{ $category?->id === $root->id ? 'bg-primary/10 font-semibold text-primary' : 'hover:bg-surface-alt' }}">
                            <span class="truncate">{{ $root->name }}</span>
                            <span class="text-xs text-muted">{{ $root->products_count + $root->children->sum('products_count') }}</span>
                        </a>
                        @if ($root->children->isNotEmpty() && $category && ($category->id === $root->id || $category->parent_id === $root->id))
                            <ul class="mt-0.5 ml-3 space-y-0.5 border-l border-line pl-2">
                                @foreach ($root->children as $child)
                                    <li><a href="{{ $site->shop('category/'.$child->slug) }}" class="flex justify-between gap-2 rounded-md px-2 py-1 {{ $category->id === $child->id ? 'font-semibold text-primary' : 'text-muted hover:text-ink' }}"><span class="truncate">{{ $child->name }}</span><span class="text-xs">{{ $child->products_count }}</span></a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </fieldset>
    @endif

    {{-- Price --}}
    <fieldset>
        <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">Harga</legend>
        <div class="grid grid-cols-2 gap-2">
            <label class="block">
                <span class="sr-only">Harga minimum</span>
                <input type="number" name="min" min="0" step="1000" inputmode="numeric" value="{{ $filters['min'] }}" placeholder="Min {{ $facets['min_price'] > 0 ? number_format($facets['min_price'], 0, ',', '.') : '' }}"
                       class="h-10 w-full rounded-btn border border-line bg-card px-3 text-sm text-ink placeholder:text-muted focus:border-primary focus:outline-none">
            </label>
            <label class="block">
                <span class="sr-only">Harga maksimum</span>
                <input type="number" name="max" min="0" step="1000" inputmode="numeric" value="{{ $filters['max'] }}" placeholder="Maks {{ $facets['max_price'] > 0 ? number_format($facets['max_price'], 0, ',', '.') : '' }}"
                       class="h-10 w-full rounded-btn border border-line bg-card px-3 text-sm text-ink placeholder:text-muted focus:border-primary focus:outline-none">
            </label>
        </div>
    </fieldset>

    {{-- Availability & promo --}}
    <fieldset class="space-y-2">
        <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">Ketersediaan</legend>
        <label class="flex cursor-pointer items-center gap-2.5">
            <input type="checkbox" name="availability" value="in_stock" @checked($filters['availability'] === 'in_stock') class="size-4 rounded border-line accent-[var(--brand-primary)]">
            Hanya yang tersedia
        </label>
        <label class="flex cursor-pointer items-center gap-2.5">
            <input type="checkbox" name="sale" value="1" @checked($filters['sale']) class="size-4 rounded border-line accent-[var(--brand-primary)]">
            Sedang diskon
        </label>
    </fieldset>

    {{-- Rating --}}
    <fieldset class="space-y-1.5">
        <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">Rating</legend>
        @foreach ([4, 3] as $stars)
            <label class="flex cursor-pointer items-center gap-2.5">
                <input type="radio" name="rating" value="{{ $stars }}" @checked($filters['rating'] === $stars) class="size-4 accent-[var(--brand-primary)]">
                <x-site.stars :rating="$stars" class="text-ink" /> <span class="text-muted">ke atas</span>
            </label>
        @endforeach
        @if ($filters['rating'])
            <label class="flex cursor-pointer items-center gap-2.5 text-muted">
                <input type="radio" name="rating" value="" class="size-4 accent-[var(--brand-primary)]"> Semua rating
            </label>
        @endif
    </fieldset>

    {{-- Brands --}}
    @if (count($facets['brands']) > 0)
        <fieldset class="space-y-1.5">
            <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">Brand</legend>
            @foreach ($facets['brands'] as $brand)
                <label class="flex cursor-pointer items-center gap-2.5">
                    <input type="checkbox" name="brand[]" value="{{ $brand }}" @checked(in_array($brand, $filters['brand'], true)) class="size-4 rounded border-line accent-[var(--brand-primary)]">
                    <span class="truncate">{{ $brand }}</span>
                </label>
            @endforeach
        </fieldset>
    @endif

    {{-- Options (Size, Color, ...) --}}
    @foreach (array_slice($facets['options'], 0, 5, true) as $optionName => $values)
        <fieldset>
            <legend class="mb-2.5 font-heading text-xs font-bold tracking-wider text-muted uppercase">{{ $optionName }}</legend>
            <div class="flex flex-wrap gap-1.5">
                @foreach (array_slice($values, 0, 20) as $value)
                    @php($checked = in_array($value, $filters['attr'][$optionName] ?? [], true))
                    <label class="cursor-pointer">
                        <input type="checkbox" name="attr[{{ $optionName }}][]" value="{{ $value }}" @checked($checked) class="peer sr-only">
                        <span class="inline-flex rounded-btn border border-line px-2.5 py-1 text-xs transition peer-checked:border-primary peer-checked:bg-primary peer-checked:text-on-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40 hover:border-ink">{{ $value }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endforeach

    <div class="flex gap-2 pt-1">
        <button type="submit" class="{{ $ds->btn('primary', 'flex-1 !py-2.5') }}">Terapkan</button>
        <a href="{{ $baseAction }}{{ $filters['q'] ? '?q='.urlencode($filters['q']) : '' }}" class="{{ $ds->btn('secondary', '!py-2.5') }}">Reset</a>
    </div>
</form>
