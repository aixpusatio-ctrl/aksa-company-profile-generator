{{-- Customer account navigation (horizontal scroller on mobile, sidebar on desktop). --}}
@php
    $accountLinks = [
        ['Ringkasan', $site->account(), 'user-circle', request()->is('account')],
        ['Pesanan', $site->account('orders'), 'receipt', request()->is('account/orders*')],
        ['Wishlist', $site->account('wishlist'), 'heart', request()->is('account/wishlist')],
        ['Ulasan', $site->account('reviews'), 'star', request()->is('account/reviews')],
        ['Alamat', $site->account('addresses'), 'map-pin', request()->is('account/addresses')],
    ];
@endphp
<aside aria-label="Menu akun" class="min-w-0">
    <div class="lg:sticky lg:top-28">
        @if ($customer)
            <div class="mb-4 hidden items-center gap-3 lg:flex">
                <span class="inline-flex size-11 items-center justify-center rounded-full bg-primary font-heading text-lg font-bold text-on-primary">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold text-ink">{{ $customer->name }}</span>
                    <span class="block truncate text-xs text-muted">{{ $customer->email }}</span>
                </span>
            </div>
        @endif
        <nav class="shop-scroll -mx-5 flex gap-2 overflow-x-auto px-5 lg:mx-0 lg:flex-col lg:gap-0.5 lg:overflow-visible lg:px-0">
            @foreach ($accountLinks as [$label, $href, $icon, $active])
                <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                   class="flex shrink-0 items-center gap-2.5 rounded-btn px-3.5 py-2 text-sm font-medium transition lg:rounded-md {{ $active ? 'bg-primary text-on-primary' : 'border border-line text-ink hover:bg-surface-alt lg:border-transparent' }}">
                    <x-shop.icon :name="$icon" class="size-4" /> {{ $label }}
                </a>
            @endforeach
            <form method="POST" action="{{ $site->account('logout') }}" class="shrink-0 lg:mt-3 lg:border-t lg:border-line lg:pt-3">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2.5 rounded-btn border border-line px-3.5 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50 lg:rounded-md lg:border-transparent">
                    <x-icon name="logout" class="size-4" /> Keluar
                </button>
            </form>
        </nav>
    </div>
</aside>
