{{--
    Quick add to cart. Simple in-stock products get an AJAX form (normal POST
    without JS); products with variants / out of stock link to the detail page.
    Vars: $product, $qaStyle (button|icon), $qaClass, $qaWrap.
--}}
@php
    $url = $site->shop('product/'.$product->slug);
    $canQuick = ! $product->hasVariants() && $product->isInStock() && $product->ctaEnabled('add_to_cart', $shop);
    $qaStyle = $qaStyle ?? 'button';
@endphp
@if ($canQuick)
    <form method="POST" action="{{ $site->shop('cart') }}" @submit.prevent="$store.shop.add($el)" class="{{ $qaWrap ?? '' }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="quantity" value="1">
        @if ($qaStyle === 'icon')
            <button type="submit" class="{{ $qaClass ?? 'inline-flex size-10 items-center justify-center rounded-full bg-primary text-on-primary shadow-lg transition hover:scale-105' }}" aria-label="Tambah {{ $product->name }} ke keranjang">
                <x-icon name="plus" class="size-5" />
            </button>
        @else
            <button type="submit" class="{{ $qaClass ?? $ds->btn('primary', 'w-full !px-3 !py-2.5 !text-xs sm:!text-sm') }}">
                <x-shop.icon name="bag" class="size-4" /> <span>+ Keranjang</span>
            </button>
        @endif
    </form>
@else
    <div class="{{ $qaWrap ?? '' }}">
        @if ($qaStyle === 'icon')
            <a href="{{ $url }}" class="{{ $qaClass ?? 'inline-flex size-10 items-center justify-center rounded-full bg-primary text-on-primary shadow-lg transition hover:scale-105' }}" aria-label="{{ $product->isInStock() ? 'Pilih varian' : 'Lihat produk' }}">
                <x-icon name="arrow-right" class="size-5" />
            </a>
        @else
            <a href="{{ $url }}" class="{{ $qaClass ?? $ds->btn($product->isInStock() ? 'secondary' : 'ghost', 'w-full !px-3 !py-2.5 !text-xs sm:!text-sm') }}">
                {{ ! $product->isInStock() ? 'Lihat produk' : ($product->hasVariants() ? 'Pilih varian' : 'Lihat detail') }}
            </a>
        @endif
    </div>
@endif
