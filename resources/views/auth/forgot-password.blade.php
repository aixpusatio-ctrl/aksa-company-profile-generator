<x-layouts.guest title="Lupa Password">
    <h1 class="text-3xl font-extrabold tracking-tight">Lupa password?</h1>
    <p class="mt-2 text-sm text-slate-500">Masukkan email Anda dan kami akan mengirimkan link untuk membuat password baru.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf
        <x-form.input name="email" type="email" label="Email" required autofocus />
        <button class="btn btn-primary btn-lg w-full">Kirim link reset</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500"><a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">&larr; Kembali ke login</a></p>
</x-layouts.guest>
