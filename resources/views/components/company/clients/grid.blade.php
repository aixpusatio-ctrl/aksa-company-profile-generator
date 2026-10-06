{{-- Clients: Grid — hairline grid of cells holding typographic client wordmarks with varied weights and tracking. --}}
@php
    $clients = collect($company->clients())->take(12)->values();
    $filler = (4 - $clients->count() % 4) % 4;
    $styles = [
        'font-heading text-xl font-bold tracking-tight',
        'font-body text-sm font-light tracking-[0.3em] uppercase',
        'font-heading text-xl font-semibold italic',
        'font-body text-lg font-black uppercase tracking-tighter',
        'font-mono text-base font-medium',
        'font-heading text-lg font-medium tracking-[0.1em] uppercase',
        'font-body text-xl font-extrabold tracking-[-0.04em]',
    ];
@endphp
@if ($clients->isNotEmpty())
<section id="clients" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Klien', 'title' => $section->title ?: 'Dipercaya oleh Klien Terkemuka', 'subtitle' => $section->subtitle, 'number' => $index])
        <ul class="mt-12 grid grid-cols-2 border-t border-l border-line sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($clients as $client)
                <li class="group flex min-h-28 items-center justify-center border-r border-b border-line px-4 py-8 text-center transition-colors hover:bg-primary/5 sm:min-h-32" {!! $ds->reveal($loop->index) !!}>
                    <span class="leading-tight text-ink/55 transition group-hover:text-ink {{ $styles[$loop->index % count($styles)] }}">{{ $client }}</span>
                </li>
            @endforeach
            @for ($i = 0; $i < $filler; $i++)
                <li class="hidden border-r border-b border-line lg:block" aria-hidden="true"></li>
            @endfor
        </ul>
    </div>
</section>
@endif
