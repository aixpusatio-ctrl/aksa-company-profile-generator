@php
    $inAccount = $inAccount ?? false;
    $accountTitle = 'Wishlist';
    $accountSubtitle = 'Produk yang Anda simpan untuk nanti.';
@endphp

@extends($inAccount ? 'websites.shop.account.layout' : 'websites.shop.layout')

@section($inAccount ? 'account' : 'shop')
    @unless ($inAccount)
        @include('websites.shop.partials.page-header', ['title' => 'Wishlist', 'subtitle' => $products->isNotEmpty() ? $products->count().' produk tersimpan' : null, 'crumbs' => ['Wishlist' => null]])
    @endunless

    <div class="{{ $inAccount ? '' : $ds->container().' py-8 sm:py-12' }}">
        @if ($products->isEmpty())
            @include('websites.shop.partials.empty', [
                'icon' => 'heart',
                'emptyTitle' => 'Wishlist masih kosong',
                'emptyText' => 'Ketuk ikon hati pada produk untuk menyimpannya di sini.',
                'actionUrl' => $site->shop('products'),
            ])
        @else
            @unless ($customer)
                <p class="mb-6 rounded-brand bg-surface-alt px-4 py-3 text-sm text-muted">
                    Wishlist tersimpan di perangkat ini. <a href="{{ $site->account('login') }}" class="font-semibold text-primary">Masuk</a> agar tersimpan di akun Anda.
                </p>
            @endunless
            <ul class="grid gap-4 sm:grid-cols-2 {{ $inAccount ? 'xl:grid-cols-3' : 'lg:grid-cols-3 xl:grid-cols-4' }}">
                @foreach ($products as $product)
                    @php($url = $site->shop('product/'.$product->slug))
                    <li class="{{ $ds->card('group relative flex gap-3 p-3', false) }} border border-line sm:flex-col">
                        <a href="{{ $url }}" class="relative block w-24 shrink-0 overflow-hidden rounded-brand bg-surface-alt sm:w-full">
                            <span class="block aspect-square"><x-site.img :src="$product->mainImage()" :alt="$product->name" icon="cube" class="size-full object-cover {{ $product->isInStock() ? '' : 'opacity-60 grayscale' }}" /></span>
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                            <a href="{{ $url }}" class="line-clamp-2 text-sm font-semibold text-ink hover:text-primary">{{ $product->name }}</a>
                            @include('websites.shop.partials.price')
                            <p class="text-xs {{ $product->isInStock() ? 'text-emerald-600' : 'text-rose-600' }}">{{ $product->availabilityLabel() }}</p>
                            <div class="mt-auto flex items-center gap-2 pt-1">
                                @if (! $product->hasVariants() && $product->isInStock())
                                    <form method="POST" action="{{ $site->shop('wishlist/'.$product->id.'/cart') }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="{{ $ds->btn('primary', 'w-full !px-3 !py-2 !text-xs') }}">Pindahkan ke keranjang</button>
                                    </form>
                                @else
                                    <a href="{{ $url }}" class="{{ $ds->btn('secondary', 'flex-1 !px-3 !py-2 !text-xs') }}">{{ $product->isInStock() ? 'Pilih varian' : 'Lihat produk' }}</a>
                                @endif
                                <form method="POST" action="{{ $site->shop('wishlist/'.$product->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex size-9 items-center justify-center rounded-full border border-line text-muted hover:border-rose-300 hover:text-rose-600" aria-label="Hapus {{ $product->name }} dari wishlist"><x-icon name="trash" class="size-4" /></button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
