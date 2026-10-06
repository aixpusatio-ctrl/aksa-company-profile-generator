@extends('websites.shop.layout')

@section('shop')
    <div class="{{ $ds->container() }} flex justify-center py-12 sm:py-16">
        <div class="w-full max-w-md">
            <div class="text-center">
                <span class="inline-flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary"><x-shop.icon name="user-circle" class="size-7" /></span>
                <h1 class="heading mt-4 text-3xl text-ink">Masuk ke akun Anda</h1>
                <p class="mt-2 text-sm text-muted">Lacak pesanan, simpan alamat dan wishlist di {{ $shop->displayName() }}.</p>
            </div>
            <form method="POST" action="{{ $site->account('login') }}" class="{{ $ds->card('mt-8 space-y-4 p-6 sm:p-8', false) }} border border-line">
                @csrf
                <div>
                    <label for="li-email" class="shop-label">Email</label>
                    <input id="li-email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="shop-input" @error('email') aria-invalid="true" @enderror>
                    @error('email')<p class="shop-error">{{ $message }}</p>@enderror
                </div>
                <div x-data="{ show: false }">
                    <label for="li-password" class="shop-label">Password</label>
                    <div class="relative">
                        <input id="li-password" :type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password" class="shop-input pr-11">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center text-muted hover:text-ink" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"><x-icon name="eye" class="size-4" /></button>
                    </div>
                    @error('password')<p class="shop-error">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2.5 text-sm text-ink">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-[var(--brand-primary)]"> Ingat saya
                </label>
                <button type="submit" class="{{ $ds->btn('primary', 'w-full !py-3.5') }}">Masuk</button>
            </form>
            <p class="mt-6 text-center text-sm text-muted">Belum punya akun? <a href="{{ $site->account('register') }}" class="font-semibold text-primary">Daftar gratis</a></p>
            <p class="mt-2 text-center text-sm text-muted">Belanja tanpa akun? <a href="{{ $site->shop('order/track') }}" class="font-semibold text-primary">Lacak pesanan</a></p>
        </div>
    </div>
@endsection
