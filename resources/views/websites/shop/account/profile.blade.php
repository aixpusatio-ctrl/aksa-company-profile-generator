@php($accountTitle = 'Akun Saya')
@extends('websites.shop.account.layout')

@section('account')
    <div class="space-y-6">
        <div class="grid grid-cols-3 gap-3">
            @foreach ([['Pesanan', $stats['orders'], 'receipt', $site->account('orders')], ['Total belanja', \App\Support\Shop\Money::format($stats['spent']), 'banknotes', $site->account('orders')], ['Wishlist', $stats['wishlist'], 'heart', $site->account('wishlist')]] as [$label, $value, $icon, $href])
                <a href="{{ $href }}" class="{{ $ds->card('p-3 sm:p-5', false) }} border border-line transition hover:border-primary">
                    <x-shop.icon :name="$icon" class="size-5 text-primary" />
                    <p class="mt-2 truncate font-heading text-base font-bold text-ink sm:text-2xl">{{ $value }}</p>
                    <p class="truncate text-xs text-muted">{{ $label }}</p>
                </a>
            @endforeach
        </div>

        <section class="{{ $ds->card('p-5 sm:p-6', false) }} border border-line">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-heading text-lg font-bold text-ink">Pesanan terbaru</h2>
                <a href="{{ $site->account('orders') }}" class="text-sm font-semibold text-primary">Lihat semua</a>
            </div>
            @forelse ($recentOrders as $o)
                <a href="{{ $site->account('orders/'.$o->order_number) }}" class="mt-3 flex items-center justify-between gap-3 rounded-brand border border-line p-3.5 text-sm transition hover:border-primary">
                    <span class="min-w-0">
                        <span class="block font-semibold text-ink">#{{ $o->order_number }}</span>
                        <span class="block text-xs text-muted">{{ $o->created_at->translatedFormat('d M Y') }}</span>
                    </span>
                    <span class="flex shrink-0 flex-col items-end gap-1">
                        <span class="font-semibold text-ink">{{ \App\Support\Shop\Money::format($o->total, $o->currency) }}</span>
                        @include('websites.shop.partials.status-badge', ['status' => $o->status])
                    </span>
                </a>
            @empty
                <p class="mt-3 text-sm text-muted">Belum ada pesanan. <a href="{{ $site->shop('products') }}" class="font-semibold text-primary">Mulai belanja</a></p>
            @endforelse
        </section>

        <section class="{{ $ds->card('p-5 sm:p-6', false) }} border border-line">
            <h2 class="font-heading text-lg font-bold text-ink">Profil</h2>
            <form method="POST" action="{{ $site->account() }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="pf-name" class="shop-label">Nama lengkap</label>
                        <input id="pf-name" name="name" value="{{ old('name', $customer->name) }}" required maxlength="120" class="shop-input" @error('name') aria-invalid="true" @enderror>
                        @error('name')<p class="shop-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pf-email" class="shop-label">Email</label>
                        <input id="pf-email" value="{{ $customer->email }}" disabled class="shop-input opacity-70">
                    </div>
                    <div>
                        <label for="pf-phone" class="shop-label">No. HP</label>
                        <input id="pf-phone" type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" maxlength="30" class="shop-input" @error('phone') aria-invalid="true" @enderror>
                        @error('phone')<p class="shop-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pf-wa" class="shop-label">WhatsApp</label>
                        <input id="pf-wa" type="tel" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}" maxlength="30" class="shop-input" @error('whatsapp') aria-invalid="true" @enderror>
                        @error('whatsapp')<p class="shop-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <details class="rounded-brand border border-line p-4" @if ($errors->hasAny(['current_password', 'password'])) open @endif>
                    <summary class="cursor-pointer text-sm font-semibold text-ink">Ganti password</summary>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label for="pf-current" class="shop-label">Password saat ini</label>
                            <input id="pf-current" type="password" name="current_password" autocomplete="current-password" class="shop-input" @error('current_password') aria-invalid="true" @enderror>
                            @error('current_password')<p class="shop-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pf-new" class="shop-label">Password baru</label>
                            <input id="pf-new" type="password" name="password" minlength="8" autocomplete="new-password" class="shop-input" @error('password') aria-invalid="true" @enderror>
                            @error('password')<p class="shop-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pf-new2" class="shop-label">Ulangi password</label>
                            <input id="pf-new2" type="password" name="password_confirmation" autocomplete="new-password" class="shop-input">
                        </div>
                    </div>
                </details>

                <button type="submit" class="{{ $ds->btn('primary') }}">Simpan Perubahan</button>
            </form>
        </section>
    </div>
@endsection
