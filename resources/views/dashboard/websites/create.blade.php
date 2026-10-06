<x-layouts.app title="Create Company Profile">
    <x-page-header title="Create New Company Profile" description="Langkah 1 dari 11 — pilih template dan beri nama perusahaan Anda." :back="route('websites.index')" />

    <form method="POST" action="{{ route('websites.store') }}" class="space-y-8">
        @csrf
        <div class="card card-body grid gap-5 md:grid-cols-2">
            <x-form.input name="name" label="Nama perusahaan" required autofocus placeholder="PT Maju Bersama Indonesia" />
            <x-form.input name="tagline" label="Tagline (opsional)" placeholder="Partner terpercaya untuk bisnis Anda" />
            <p class="form-help md:col-span-2">Subdomain dibuat otomatis dari nama perusahaan (contoh: <strong>pt-maju-bersama.{{ config('platform.domain') }}</strong>) dan bisa diubah nanti.</p>
        </div>

        <div>
            <h2 class="mb-4 text-lg font-bold">1. Choose Template</h2>
            @error('template')<p class="form-error mb-3">{{ $message }}</p>@enderror
            @include('dashboard.websites.partials.template-picker')
        </div>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <button class="btn btn-primary btn-lg shadow-xl shadow-brand-600/30">Lanjutkan <x-icon name="arrow-right" class="size-4" /></button>
        </div>
    </form>
</x-layouts.app>
