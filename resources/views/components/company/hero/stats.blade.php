{{-- Hero: Stats — data-first: headline left, large 2x2 counter grid right on a technical grid background. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 4);
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"
        style="background-image: linear-gradient(to right, var(--line) 1px, transparent 1px), linear-gradient(to bottom, var(--line) 1px, transparent 1px), linear-gradient(to right, color-mix(in oklab, var(--line) 50%, transparent) 1px, transparent 1px), linear-gradient(to bottom, color-mix(in oklab, var(--line) 50%, transparent) 1px, transparent 1px); background-size: 96px 96px, 96px 96px, 24px 24px, 24px 24px; -webkit-mask-image: linear-gradient(to bottom, #000 40%, transparent); mask-image: linear-gradient(to bottom, #000 40%, transparent)"></div>

    <div class="{{ $ds->container('wide') }} grid items-center gap-14 pt-32 pb-16 lg:grid-cols-12 lg:gap-12 lg:pt-40 lg:pb-24">
        <div class="lg:col-span-6">
            <p class="font-mono text-xs tracking-[0.2em] text-muted uppercase" {!! $ds->reveal(0) !!}>
                <span class="text-primary">●</span> {{ $company->name }}{{ $company->city ? ' / '.$company->city : '' }}
            </p>
            <h1 class="heading mt-6 text-[min(var(--display),4.75rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Tim Kami', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Proyek', 'kind' => 'secondary'])
            </div>
        </div>

        <div class="lg:col-span-6" {!! $ds->reveal(2, 'right') !!}>
            @if ($stats)
                <dl class="grid grid-cols-2 border-t border-l border-line bg-surface/70 backdrop-blur-sm">
                    @foreach ($stats as $stat)
                        <div class="relative border-r border-b border-line p-5 sm:p-8 lg:p-10">
                            <span class="absolute top-3 right-4 font-mono text-[10px] text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <dd class="heading text-[clamp(1.75rem,3.4vw,3.5rem)] leading-none text-ink lg:whitespace-nowrap" data-count>{{ $stat['value'] }}</dd>
                            <dt class="mt-3 text-sm text-muted">{{ $stat['label'] }}</dt>
                            <span class="mt-5 block h-0.5 w-8 bg-primary"></span>
                        </div>
                    @endforeach
                    @if (count($stats) % 2)
                        <a href="{{ $site->anchor('about') }}" class="group flex flex-col justify-between border-r border-b border-line bg-primary/5 p-5 transition hover:bg-primary/10 sm:p-8 lg:p-10">
                            <x-icon name="arrow-up-right" class="size-6 text-primary transition group-hover:translate-x-1 group-hover:-translate-y-1" />
                            <span class="mt-6 text-sm font-semibold text-ink">Tentang perusahaan</span>
                        </a>
                    @endif
                </dl>
            @else
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="chart" class="{{ $ds->img('aspect-[4/3] w-full object-cover') }}" />
            @endif
        </div>
    </div>
</section>
