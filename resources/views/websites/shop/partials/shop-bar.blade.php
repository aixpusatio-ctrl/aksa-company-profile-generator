{{-- Shop sub-navigation: search (with live suggestions), categories and cart / wishlist / account shortcuts. --}}
@php
    $cartIsDrawer = $ds->get('cart') === 'drawer';
    $currentCategory = $category ?? null;
@endphp
<div class="border-b border-line bg-surface/95 text-ink">
    <div class="{{ $ds->container() }} flex items-center gap-3 py-3 sm:gap-5">
        <a href="{{ $site->shop() }}" class="hidden shrink-0 items-center gap-2 font-heading text-base font-bold text-ink sm:inline-flex">
            <x-shop.icon name="bag" class="size-5 text-primary" />
            <span class="max-w-40 truncate">{{ $shop->displayName() }}</span>
        </a>

        {{-- Search --}}
        <form action="{{ $site->shop('products') }}" method="GET" role="search" class="relative min-w-0 flex-1"
              x-data="shopSearch(@js($site->shop('search')))" @click.outside="open = false" @keydown.escape="open = false">
            <label for="shop-search" class="sr-only">Cari produk</label>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-muted" />
            <input id="shop-search" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari produk…" autocomplete="off"
                   x-model="q" @input.debounce.250ms="search()" @focus="open = results.length > 0"
                   class="h-10 w-full rounded-btn border border-line bg-card pr-3 pl-10 text-sm text-ink placeholder:text-muted focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none">
            <div x-cloak x-show="open" x-transition.opacity class="absolute inset-x-0 top-full z-30 mt-2 overflow-hidden rounded-brand border border-line bg-card shadow-2xl shadow-black/10">
                <template x-for="item in results" :key="item.url">
                    <a :href="item.url" class="flex items-center gap-3 px-3 py-2.5 hover:bg-surface-alt">
                        <span class="size-11 shrink-0 overflow-hidden rounded-md bg-surface-alt">
                            <img x-show="item.image" :src="item.image" alt="" class="size-full object-cover">
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-ink" x-text="item.name"></span>
                            <span class="block text-xs text-muted"><span class="font-semibold text-ink" x-text="item.price"></span> <span x-show="!item.available" class="text-rose-500">· Stok habis</span></span>
                        </span>
                    </a>
                </template>
                <p x-show="results.length === 0 && !loading" class="px-4 py-3 text-sm text-muted">Produk tidak ditemukan.</p>
                <a :href="allUrl" x-show="results.length > 0" class="block border-t border-line px-4 py-2.5 text-center text-sm font-semibold text-primary hover:bg-surface-alt">Lihat semua hasil</a>
            </div>
        </form>

        {{-- Shortcuts --}}
        <nav class="flex shrink-0 items-center gap-0.5 sm:gap-1" aria-label="Akun & keranjang">
            <a href="{{ $site->shop('wishlist') }}" class="relative inline-flex size-10 items-center justify-center rounded-full text-ink transition hover:bg-surface-alt" aria-label="Wishlist">
                <x-icon name="heart" class="size-5" />
                <span data-wishlist-count class="absolute -top-0.5 -right-0.5 min-w-4.5 items-center justify-center rounded-full bg-primary px-1 text-[10px] leading-4 font-bold text-on-primary {{ count($wishlistIds) ? 'inline-flex' : 'hidden' }}">{{ count($wishlistIds) }}</span>
            </a>
            <a href="{{ $customer ? $site->account() : $site->account('login') }}" class="hidden size-10 items-center justify-center rounded-full text-ink transition hover:bg-surface-alt sm:inline-flex" aria-label="{{ $customer ? 'Akun saya' : 'Masuk' }}">
                <x-shop.icon name="user-circle" class="size-5" />
            </a>
            <a href="{{ $site->shop('cart') }}" class="relative inline-flex size-10 items-center justify-center rounded-full text-ink transition hover:bg-surface-alt" aria-label="Keranjang"
               @if ($cartIsDrawer) @click.prevent="$store.shop ? $store.shop.openDrawer() : (window.location = $el.href)" @endif>
                <x-shop.icon name="bag" class="size-5" />
                <span data-cart-count class="absolute -top-0.5 -right-0.5 min-w-4.5 items-center justify-center rounded-full bg-primary px-1 text-[10px] leading-4 font-bold text-on-primary {{ $cartCount ? 'inline-flex' : 'hidden' }}">{{ $cartCount }}</span>
            </a>
        </nav>
    </div>

    {{-- Categories --}}
    @if ($categoriesTree->isNotEmpty())
        <div class="{{ $ds->container() }}">
            <nav class="shop-scroll -mb-px flex gap-1 overflow-x-auto pb-2 text-sm" aria-label="Kategori">
                <a href="{{ $site->shop() }}" class="shrink-0 rounded-full px-3.5 py-1.5 font-medium transition {{ request()->is('shop') ? 'bg-ink text-surface' : 'text-muted hover:bg-surface-alt hover:text-ink' }}">Beranda Shop</a>
                <a href="{{ $site->shop('products') }}" class="shrink-0 rounded-full px-3.5 py-1.5 font-medium transition {{ request()->is('shop/products') ? 'bg-ink text-surface' : 'text-muted hover:bg-surface-alt hover:text-ink' }}">Semua Produk</a>
                @foreach ($categoriesTree as $root)
                    @php($active = $currentCategory && ($currentCategory->id === $root->id || $currentCategory->parent_id === $root->id))
                    <a href="{{ $site->shop('category/'.$root->slug) }}" class="shrink-0 rounded-full px-3.5 py-1.5 font-medium transition {{ $active ? 'bg-ink text-surface' : 'text-muted hover:bg-surface-alt hover:text-ink' }}">{{ $root->name }}</a>
                @endforeach
            </nav>
        </div>
    @endif
</div>
