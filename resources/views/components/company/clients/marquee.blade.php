{{-- Clients: Marquee — infinite scrolling row of typographic client wordmarks with masked fade edges. --}}
@php
    $clients = collect($company->clients())->take(16);
    $row = collect();
    while ($clients->isNotEmpty() && $row->count() < 8) {
        $row = $row->concat($clients);
    }
    $styles = [
        'font-heading font-bold tracking-tight',
        'font-body font-light tracking-[0.25em] uppercase text-base sm:text-lg',
        'font-heading font-semibold italic',
        'font-body font-black uppercase tracking-tighter',
        'font-mono font-medium tracking-wide text-base sm:text-lg',
        'font-heading font-medium tracking-[0.08em] uppercase',
    ];
@endphp
@if ($clients->isNotEmpty())
<section id="clients" class="{{ $ds->section($tone, '!py-12 sm:!py-16') }}">
    <p class="px-5 text-center text-xs font-semibold tracking-[0.25em] text-muted uppercase" {!! $ds->reveal() !!}>{{ $section->title ?: 'Dipercaya oleh perusahaan & mitra terkemuka' }}</p>
    <div class="group mt-8 flex overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_12%,black_88%,transparent)]" {!! $ds->reveal(1) !!}>
        <div class="flex w-max shrink-0 animate-marquee items-center group-hover:[animation-play-state:paused] motion-reduce:animate-none">
            @foreach ([false, true] as $dup)
                <ul class="flex shrink-0 items-center gap-x-14 pr-14 sm:gap-x-20 sm:pr-20" @if ($dup) aria-hidden="true" @endif>
                    @foreach ($row as $client)
                        <li class="text-xl whitespace-nowrap text-ink/45 transition hover:text-ink sm:text-2xl {{ $styles[$loop->index % count($styles)] }}">{{ $client }}</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</section>
@endif
