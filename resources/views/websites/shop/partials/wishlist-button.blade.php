{{-- Wishlist heart toggle (works without JS as a normal POST). Vars: $product, $wishClass (button), $wishWrap (form). --}}
@php($inWish = in_array($product->id, $wishlistIds ?? []))
<form method="POST" action="{{ $site->shop('wishlist/'.$product->id) }}" @submit.prevent="$store.shop.toggleWishlist({{ $product->id }}, $el)" class="{{ $wishWrap ?? 'absolute top-2.5 right-2.5 z-10' }}">
    @csrf
    <button type="submit" class="shop-wish {{ $wishClass ?? 'inline-flex size-9 items-center justify-center rounded-full bg-white/90 text-neutral-800 shadow-sm backdrop-blur transition hover:scale-110 hover:text-rose-500' }}"
            @if ($inWish) data-active @endif :data-active="$store.shop.inWishlist({{ $product->id }}) ? '' : null"
            :aria-pressed="$store.shop.inWishlist({{ $product->id }}).toString()" aria-pressed="{{ $inWish ? 'true' : 'false' }}"
            aria-label="Wishlist: {{ $product->name }}">
        <x-icon name="heart" class="wish-off size-[1.1rem]" />
        <x-shop.icon name="heart-solid" class="wish-on size-[1.1rem] text-rose-500" />
    </button>
</form>
