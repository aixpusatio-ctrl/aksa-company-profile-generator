{{--
    Shop widgets for every live page of a shop-enabled website (included from
    websites.partials.preview-bar): JS config, floating cart button, slide-in
    cart drawer (when the design uses cart = drawer) and the toast stack.
--}}
@php
    $wds = $ds ?? \App\Support\Website\DesignSystem::forCraftedLayout($company->layout());
    $wCartCount = $cartCount ?? app(\App\Services\Shop\CartService::class)->count($company);
    $wWishlist = $wishlistIds ?? app(\App\Services\Shop\WishlistService::class)->ids($company);
    $wDrawer = $wds->get('cart') === 'drawer';
    $flashError = $errors->first('product') ?: $errors->first('variant') ?: $errors->first('cart') ?: $errors->first('code');
@endphp
<script type="application/json" id="shop-config">{!! json_encode([
    'drawer' => $wDrawer,
    'count' => $wCartCount,
    'wishlist' => array_values($wWishlist),
    'urls' => [
        'cart' => $site->shop('cart'),
        'summary' => $site->shop('cart/summary'),
        'wishlist' => $site->shop('wishlist'),
        'wishlistPage' => $site->shop('wishlist'),
        'checkout' => $site->shop('checkout'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

<div x-data class="shop-widgets theme-{{ $wds->get('theme') }} type-{{ $wds->get('heading') }} font-body text-ink">
    {{-- Floating cart --}}
    <a href="{{ $site->shop('cart') }}" aria-label="Keranjang belanja"
       x-show="$store.shop.count > 0" @if ($wCartCount === 0) x-cloak @endif
       @if ($wDrawer) @click.prevent="$store.shop.openDrawer()" @endif
       class="shop-float-cart fixed right-5 z-40 inline-flex size-14 items-center justify-center rounded-full bg-primary text-on-primary shadow-lg shadow-black/25 transition hover:scale-105 {{ $company->whatsappUrl() ? 'bottom-22' : 'bottom-5' }}">
        <x-shop.icon name="bag" class="size-6" />
        <span data-cart-count class="absolute -top-1 -right-1 min-w-5.5 items-center justify-center rounded-full bg-ink px-1.5 text-[11px] leading-5.5 font-bold text-surface ring-2 ring-white {{ $wCartCount ? 'inline-flex' : 'hidden' }}">{{ $wCartCount }}</span>
    </a>

    {{-- Cart drawer --}}
    @if ($wDrawer)
        <div x-cloak x-show="$store.shop.drawer" class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Keranjang belanja"
             @keydown.escape.window="$store.shop.drawer = false">
            <div x-show="$store.shop.drawer" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" @click="$store.shop.drawer = false"></div>
            <aside x-show="$store.shop.drawer" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                   class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-surface text-ink shadow-2xl">
                <header class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="font-heading text-lg font-bold">Keranjang <span class="text-sm font-normal text-muted" x-text="'(' + $store.shop.count + ')'"></span></h2>
                    <button type="button" class="inline-flex size-9 items-center justify-center rounded-full hover:bg-surface-alt" @click="$store.shop.drawer = false" aria-label="Tutup">
                        <x-icon name="x" class="size-5" />
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4">
                    <template x-if="$store.shop.loadingCart && !$store.shop.cart">
                        <div class="space-y-4">
                            <div class="h-20 animate-pulse rounded-brand bg-surface-alt"></div>
                            <div class="h-20 animate-pulse rounded-brand bg-surface-alt"></div>
                        </div>
                    </template>
                    <template x-if="$store.shop.cart && $store.shop.cart.items.length === 0">
                        <div class="flex flex-col items-center py-16 text-center">
                            <x-shop.icon name="bag" class="size-12 text-muted" />
                            <p class="mt-4 font-semibold">Keranjang masih kosong</p>
                            <a href="{{ $site->shop('products') }}" class="mt-4 text-sm font-semibold text-primary">Mulai belanja →</a>
                        </div>
                    </template>
                    <template x-if="$store.shop.cart && $store.shop.cart.errors.length">
                        <div class="mb-4 rounded-brand border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            <template x-for="err in $store.shop.cart.errors"><p x-text="err"></p></template>
                        </div>
                    </template>
                    <ul class="divide-y divide-line" x-show="$store.shop.cart">
                        <template x-for="item in ($store.shop.cart ? $store.shop.cart.items : [])" :key="item.id">
                            <li class="flex gap-3 py-4">
                                <a :href="item.url" class="size-20 shrink-0 overflow-hidden rounded-brand bg-surface-alt">
                                    <img x-show="item.image" :src="item.image" :alt="item.name" class="size-full object-cover">
                                </a>
                                <div class="min-w-0 flex-1">
                                    <a :href="item.url" class="line-clamp-2 text-sm font-semibold hover:text-primary" x-text="item.name"></a>
                                    <p class="text-xs text-muted" x-show="item.variant" x-text="item.variant"></p>
                                    <p class="mt-1 text-xs text-rose-500" x-show="item.error" x-text="item.error"></p>
                                    <div class="mt-2 flex items-center justify-between gap-2">
                                        <div class="inline-flex items-center rounded-btn border border-line">
                                            <button type="button" class="inline-flex size-8 items-center justify-center hover:text-primary" aria-label="Kurangi"
                                                    @click="item.quantity > 1 ? $store.shop.updateItem(item.id, item.quantity - 1) : $store.shop.removeItem(item.id)"><x-shop.icon name="minus" class="size-3.5" /></button>
                                            <span class="w-7 text-center text-sm font-semibold" x-text="item.quantity"></span>
                                            <button type="button" class="inline-flex size-8 items-center justify-center hover:text-primary disabled:opacity-30" aria-label="Tambah"
                                                    :disabled="item.quantity >= item.max" @click="$store.shop.updateItem(item.id, item.quantity + 1)"><x-icon name="plus" class="size-3.5" /></button>
                                        </div>
                                        <span class="text-sm font-bold" x-text="item.line_total"></span>
                                    </div>
                                </div>
                                <button type="button" class="self-start text-muted hover:text-rose-500" aria-label="Hapus" @click="$store.shop.removeItem(item.id)"><x-icon name="trash" class="size-4" /></button>
                            </li>
                        </template>
                    </ul>
                </div>

                <footer class="border-t border-line px-5 py-4" x-show="$store.shop.cart && $store.shop.cart.items.length">
                    <dl class="space-y-1.5 text-sm">
                        <div class="flex justify-between"><dt class="text-muted">Subtotal</dt><dd class="font-semibold" x-text="$store.shop.cart?.subtotal"></dd></div>
                        <div class="flex justify-between" x-show="$store.shop.cart?.discount"><dt class="text-muted">Diskon <span x-text="$store.shop.cart?.coupon ? '(' + $store.shop.cart.coupon + ')' : ''"></span></dt><dd class="font-semibold text-emerald-600" x-text="'-' + $store.shop.cart?.discount"></dd></div>
                        <div class="flex justify-between border-t border-line pt-2 text-base"><dt class="font-semibold">Total</dt><dd class="font-heading font-bold" x-text="$store.shop.cart?.total"></dd></div>
                    </dl>
                    <p class="mt-1 text-xs text-muted">Ongkir & pajak dihitung saat checkout.</p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <a href="{{ $site->shop('cart') }}" class="{{ $wds->btn('secondary', 'w-full') }}">Lihat Keranjang</a>
                        <a href="{{ $site->shop('checkout') }}" class="{{ $wds->btn('primary', 'w-full') }}">Checkout</a>
                    </div>
                </footer>
            </aside>
        </div>
    @endif

    {{-- Toasts --}}
    <div class="pointer-events-none fixed inset-x-0 top-4 z-[80] flex flex-col items-center gap-2 px-4 sm:inset-x-auto sm:right-5 sm:items-end" aria-live="polite">
        @if (session('shop_toast') || $flashError)
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity
                 class="shop-toast pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-brand border px-4 py-3 text-sm shadow-xl {{ $flashError ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-line bg-card text-ink' }}">
                <x-icon :name="$flashError ? 'warning' : 'check-circle'" class="mt-0.5 size-5 shrink-0 {{ $flashError ? 'text-rose-500' : 'text-emerald-500' }}" />
                <p class="flex-1">{{ $flashError ?: session('shop_toast') }}</p>
                <button type="button" @click="show = false" class="text-current/60 hover:text-current" aria-label="Tutup"><x-icon name="x" class="size-4" /></button>
            </div>
        @endif
        <template x-for="t in $store.shop.toasts" :key="t.id">
            <div x-transition.opacity class="shop-toast pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-brand border px-4 py-3 text-sm shadow-xl"
                 :class="t.type === 'error' ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-line bg-card text-ink'">
                <span class="mt-0.5 shrink-0" :class="t.type === 'error' ? 'text-rose-500' : 'text-emerald-500'">
                    <x-icon name="check-circle" class="size-5" x-show="t.type !== 'error'" />
                    <x-icon name="warning" class="size-5" x-show="t.type === 'error'" />
                </span>
                <p class="flex-1">
                    <span x-text="t.message"></span>
                    <template x-if="t.link"><a :href="t.link.href" class="ml-1 font-semibold text-primary underline-offset-2 hover:underline" x-text="t.link.label"></a></template>
                </p>
                <button type="button" @click="$store.shop.dismiss(t.id)" class="opacity-60 hover:opacity-100" aria-label="Tutup"><x-icon name="x" class="size-4" /></button>
            </div>
        </template>
    </div>
</div>
