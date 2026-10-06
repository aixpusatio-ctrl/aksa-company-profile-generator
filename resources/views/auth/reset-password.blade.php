<x-layouts.guest title="Reset Password">
    <h1 class="text-3xl font-extrabold tracking-tight">Buat password baru</h1>

    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form.input name="email" type="email" label="Email" :value="$email" required />
        <x-form.input name="password" type="password" label="Password baru" required autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" label="Konfirmasi password" required autocomplete="new-password" />
        <button class="btn btn-primary btn-lg w-full">Simpan password</button>
    </form>
</x-layouts.guest>
