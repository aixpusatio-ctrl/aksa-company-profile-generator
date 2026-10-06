{{-- Projects: Horizontal — scroll-snap rail of large project cards with year & category chips and prev/next arrows. --}}
@php
    // Rail spans the full section width; its inline padding lines the first card up with the container.
    $railPad = match ($ds->get('container')) {
        'wide' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3rem] min-[88rem]:[--rail:calc((100%-88rem)/2+3rem)]',
        'narrow' => '[--rail:1.25rem] sm:[--rail:2rem] min-[64rem]:[--rail:calc((100%-64rem)/2+2rem)]',
        'full' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3.5rem]',
        default => '[--rail:1.25rem] sm:[--rail:2rem] min-[80rem]:[--rail:calc((100%-80rem)/2+2rem)]',
    };
@endphp
<section id="projects" class="{{ $ds->section($tone, 'overflow-hidden') }}"
    x-data="{ atStart: true, atEnd: false, update() { const r = this.$refs.rail; this.atStart = r.scrollLeft < 8; this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 8 }, go(dir) { const r = this.$refs.rail; r.scrollBy({ left: dir * r.clientWidth * 0.8, behavior: 'smooth' }) } }"
    x-init="$nextTick(() => update())">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Proyek Terbaru', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->projects->count() > 1)
                <div class="flex shrink-0 items-center gap-2" {!! $ds->reveal(1) !!}>
                    <span class="mr-3 hidden font-mono text-xs text-muted sm:inline">{{ str_pad($company->projects->count(), 2, '0', STR_PAD_LEFT) }} proyek</span>
                    <button type="button" @click="go(-1)" :disabled="atStart" aria-label="Proyek sebelumnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(1)" :disabled="atEnd" aria-label="Proyek berikutnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

    </div>

        <div x-ref="rail" @scroll.debounce.60ms="update()" tabindex="0" role="region" aria-label="Daftar proyek"
            class="{{ $railPad }} mt-12 flex snap-x snap-mandatory scroll-px-[var(--rail)] gap-5 overflow-x-auto px-[var(--rail)] pb-4 [scrollbar-width:none] focus:outline-none lg:gap-8 [&::-webkit-scrollbar]:hidden">
            @foreach ($company->projects as $project)
                <article class="group w-[86%] shrink-0 snap-start sm:w-[62%] lg:w-[min(46%,38rem)]" {!! $ds->reveal(min($loop->index, 3)) !!}>
                    <div class="relative overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover transition duration-[1200ms] group-hover:scale-105 lg:aspect-[16/11]" />
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            @if ($project->year)<span class="rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $project->year }}</span>@endif
                            @if ($project->category)<span class="rounded-full bg-black/40 px-3 py-1 text-xs font-medium text-white ring-1 ring-white/30 backdrop-blur">{{ $project->category }}</span>@endif
                        </div>
                    </div>
                    <div class="mt-6 flex items-start justify-between gap-6">
                        <div class="min-w-0">
                            <h3 class="heading text-2xl break-words sm:text-3xl">{{ $project->title }}</h3>
                            @if ($project->client || $project->location)
                                <p class="mt-2 text-sm text-muted">{{ collect([$project->client, $project->location])->filter()->join(' · ') }}</p>
                            @endif
                            @if ($project->description)<p class="mt-3 line-clamp-2 max-w-lg text-sm leading-relaxed text-muted">{{ $project->description }}</p>@endif
                        </div>
                        @if ($project->url)
                            <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" aria-label="Lihat proyek {{ $project->title }}" class="inline-flex size-12 shrink-0 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary"><x-icon name="arrow-up-right" class="size-5" /></a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
</section>
