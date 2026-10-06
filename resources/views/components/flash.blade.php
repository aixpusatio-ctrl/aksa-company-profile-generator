@props(['summary' => true])
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
        <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0" />
        <p class="flex-1">{{ session('success') }}</p>
        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800" aria-label="Tutup"><x-icon name="x" class="size-4" /></button>
    </div>
@endif
@if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
        <x-icon name="x-circle" class="mt-0.5 size-5 shrink-0" />
        <p class="flex-1">{{ session('error') }}</p>
        <button @click="show = false" class="text-rose-600" aria-label="Tutup"><x-icon name="x" class="size-4" /></button>
    </div>
@endif
@if (session('status'))
    <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800" role="status">{{ session('status') }}</div>
@endif
@if ($summary && $errors->any())
    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
        <p class="font-semibold">Periksa kembali isian Anda:</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
