@props(['title', 'description' => null, 'back' => null])
<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
        @endif
        <h1 class="truncate text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1.5 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
