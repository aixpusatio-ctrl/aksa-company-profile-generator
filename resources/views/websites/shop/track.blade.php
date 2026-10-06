@extends('websites.shop.layout')

@section('shop')
    @include('websites.shop.partials.page-header', ['title' => 'Lacak Pesanan', 'subtitle' => 'Masukkan nomor pesanan dan email yang dipakai saat checkout.', 'crumbs' => ['Lacak pesanan' => null]])

    <div class="{{ $ds->container('text') }} py-10 sm:py-14">
        <form method="POST" action="{{ $site->shop('order/track') }}" class="{{ $ds->card('space-y-5 p-6 sm:p-8', false) }} border border-line">
            @csrf
            <div class="flex items-center gap-3">
                <span class="inline-flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary"><x-icon name="truck" class="size-6" /></span>
                <p class="text-sm text-muted">Nomor pesanan tercantum di halaman konfirmasi dan pesan WhatsApp Anda, contoh <span class="font-mono text-ink">{{ $shop->order_prefix ?: 'ORD' }}-000123</span>.</p>
            </div>
            <div>
                <label for="tr-number" class="shop-label">Nomor pesanan</label>
                <input id="tr-number" name="order_number" value="{{ old('order_number') }}" required maxlength="40" autocomplete="off" class="shop-input uppercase" @error('order_number') aria-invalid="true" @enderror>
                @error('order_number')<p class="shop-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tr-email" class="shop-label">Email</label>
                <input id="tr-email" type="email" name="email" value="{{ old('email', $customer?->email) }}" required class="shop-input" @error('email') aria-invalid="true" @enderror>
                @error('email')<p class="shop-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="{{ $ds->btn('primary', 'w-full !py-3.5') }}"><x-icon name="search" class="size-4" /> Lacak Pesanan</button>
            @if ($customer)
                <p class="text-center text-sm text-muted">Atau lihat <a href="{{ $site->account('orders') }}" class="font-semibold text-primary">semua pesanan di akun Anda</a>.</p>
            @else
                <p class="text-center text-sm text-muted">Punya akun? <a href="{{ $site->account('login') }}" class="font-semibold text-primary">Masuk</a> untuk melihat riwayat pesanan.</p>
            @endif
        </form>
    </div>
@endsection
