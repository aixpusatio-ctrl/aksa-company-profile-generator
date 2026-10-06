@extends('websites.shop.layout')

@section('shop')
    <div class="{{ $ds->container() }} flex justify-center py-12 sm:py-16">
        <div class="w-full max-w-md">
            <div class="text-center">
                <span class="inline-flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary"><x-shop.icon name="user-circle" class="size-7" /></span>
                <h1 class="heading mt-4 text-3xl text-ink">Buat akun</h1>
                <p class="mt-2 text-sm text-muted">Checkout lebih cepat dan pantau semua pesanan Anda.</p>
            </div>
            <form method="POST" action="{{ $site->account('register') }}" class="{{ $ds->card('mt-8 space-y-4 p-6 sm:p-8', false) }} border border-line">
                @csrf
                <div>
                    <label for="rg-name" class="shop-label">Nama lengkap</label>
                    <input id="rg-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="shop-input" @error('name') aria-invalid="true" @enderror>
                    @error('name')<p class="shop-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="rg-email" class="shop-label">Email</label>
                    <input id="rg-email" type="email" name="email" value="{{ old('email') }}" required maxlength="150" autocomplete="email" class="shop-input" @error('email') aria-invalid="true" @enderror>
                    @error('email')<p class="shop-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="rg-phone" class="shop-label">No. HP <span class="font-normal text-muted">(opsional)</span></label>
                    <input id="rg-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="30" pattern="[0-9+\s\-\(\)]+" autocomplete="tel" class="shop-input" @error('phone') aria-invalid="true" @enderror>
                    @error('phone')<p class="shop-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="rg-password" class="shop-label">Password</label>
                        <input id="rg-password" type="password" name="password" required minlength="8" autocomplete="new-password" class="shop-input" @error('password') aria-invalid="true" @enderror>
                    </div>
                    <div>
                        <label for="rg-password2" class="shop-label">Ulangi password</label>
                        <input id="rg-password2" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="shop-input">
                    </div>
                </div>
                @error('password')<p class="shop-error">{{ $message }}</p>@enderror
                <p class="text-xs text-muted">Minimal 8 karakter.</p>
                <button type="submit" class="{{ $ds->btn('primary', 'w-full !py-3.5') }}">Daftar</button>
            </form>
            <p class="mt-6 text-center text-sm text-muted">Sudah punya akun? <a href="{{ $site->account('login') }}" class="font-semibold text-primary">Masuk</a></p>
        </div>
    </div>
@endsection
