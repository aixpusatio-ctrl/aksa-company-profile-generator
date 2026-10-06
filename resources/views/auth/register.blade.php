<x-layouts.guest title="Daftar">
    <h1 class="text-3xl font-extrabold tracking-tight">Buat akun gratis</h1>
    <p class="mt-2 text-sm text-slate-500">Coba semua fitur Professional gratis selama {{ config('platform.trial_days') }} hari. Tanpa kartu kredit.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf
        <x-form.input name="name" label="Nama lengkap" required autofocus autocomplete="name" />
        <x-form.input name="email" type="email" label="Email" required autocomplete="username" />
        <x-form.input name="password" type="password" label="Password" required autocomplete="new-password" help="Minimal 8 karakter." />
        <x-form.input name="password_confirmation" type="password" label="Konfirmasi password" required autocomplete="new-password" />
        <div>
            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="terms" value="1" class="form-checkbox mt-0.5" @checked(old('terms'))>
                <span>Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi.</span>
            </label>
            @error('terms')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <button class="btn btn-primary btn-lg w-full">Daftar & mulai buat website</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Masuk</a></p>
</x-layouts.guest>
