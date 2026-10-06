<x-layouts.guest title="Masuk">
    <h1 class="text-3xl font-extrabold tracking-tight">Selamat datang kembali</h1>
    <p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola company profile Anda.</p>

    @if ($demoAccounts)
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4" x-data>
            <p class="flex items-center gap-2 text-xs font-bold tracking-wide text-amber-800 uppercase"><x-icon name="info" class="size-4" /> Akun demo (development)</p>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach ($demoAccounts as $account)
                    <button type="button" class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-left text-xs transition hover:border-amber-400"
                            @click="$refs.email.value = @js($account['email']); $refs.password.value = @js($account['password'])">
                        <span class="block font-semibold text-slate-900">{{ $account['role'] }}</span>
                        <span class="block text-slate-500">{{ $account['email'] }} / {{ $account['password'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data>
        @csrf
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" x-ref="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="form-label">Password</label>
                <a href="{{ route('password.request') }}" class="mb-1.5 text-xs font-semibold text-brand-600 hover:underline">Lupa password?</a>
            </div>
            <input id="password" x-ref="password" name="password" type="password" required autocomplete="current-password" class="form-input">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="form-checkbox"> Ingat saya
        </label>
        <button class="btn btn-primary btn-lg w-full">Masuk</button>
    </form>

    @if (setting('registration_enabled', true))
        <p class="mt-8 text-center text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Daftar gratis</a></p>
    @endif
</x-layouts.guest>
