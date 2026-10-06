{{-- Shop home section heading. Vars: $eyebrow, $heading, $more (url|null), $moreLabel, $n (number). --}}
<div class="mb-7 flex flex-wrap items-end justify-between gap-4 sm:mb-9">
    <div class="min-w-0">
        {!! $ds->eyebrow($eyebrow ?? null, $n ?? 1) !!}
        <h2 class="heading mt-2 text-2xl text-ink sm:text-3xl">{{ $heading }}</h2>
    </div>
    @if (! empty($more))
        <a href="{{ $more }}" class="{{ $ds->btn('link') }}">{{ $moreLabel ?? 'Lihat semua' }} <x-icon name="arrow-right" class="size-4" /></a>
    @endif
</div>
