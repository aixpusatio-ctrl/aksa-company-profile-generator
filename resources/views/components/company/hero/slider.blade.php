{{-- Hero: Slider — full-width crossfading photo slider (hero + projects) with captions, dots, arrows and autoplay. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $slides = collect([['src' => $company->url('hero_image'), 'caption' => $company->name, 'meta' => $company->city]])
        ->merge($company->projects->filter(fn ($p) => $p->image)->take(3)->map(fn ($p) => [
            'src' => $p->url('image'),
            'caption' => $p->title,
            'meta' => collect([$p->location, $p->year])->filter()->implode(' · '),
        ]))
        ->values();
    $count = $slides->count();
@endphp
<section id="hero" class="relative isolate flex min-h-[90vh] items-end overflow-hidden bg-black text-white"
    style="--ink:#fff;--muted:rgba(255,255,255,.78);--line:rgba(255,255,255,.3);--card:rgba(255,255,255,.08)"
    x-data="{
        i: 0, n: {{ $count }}, timer: null,
        go(k) { this.i = (k + this.n) % this.n },
        start() { if (this.n < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; this.stop(); this.timer = setInterval(() => this.go(this.i + 1), 6000) },
        stop() { clearInterval(this.timer) },
    }"
    x-init="start()" @mouseenter="stop()" @mouseleave="start()" @focusin="stop()"
    aria-roledescription="carousel" aria-label="Sorotan {{ $company->name }}">
    @foreach ($slides as $slide)
        <div class="absolute inset-0 -z-20 transition-opacity duration-1000 ease-out {{ $loop->first ? 'opacity-100' : 'opacity-0' }}"
            :class="i === {{ $loop->index }} ? '!opacity-100' : '!opacity-0'" aria-roledescription="slide" :aria-hidden="i !== {{ $loop->index }}">
            <x-site.img :src="$slide['src']" :alt="$slide['caption']" icon="building" class="size-full object-cover {{ $loop->first ? 'animate-ken-burns' : '' }}" />
        </div>
    @endforeach
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/85 via-black/35 to-black/40"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/50 to-transparent"></div>

    <div class="{{ $ds->container('wide') }} relative pt-36 pb-12 lg:pb-16">
        <div class="max-w-3xl">
            <div class="text-xs font-semibold tracking-[0.25em] text-white/80 uppercase" {!! $ds->reveal(0) !!}>{{ $company->name }}</div>
            <h1 class="heading mt-5 text-display text-white max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-6 max-w-2xl text-lead text-white/80" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Jadwalkan Kunjungan', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Proyek', 'kind' => 'secondary', 'class' => 'backdrop-blur'])
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-6 border-t border-white/25 pt-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative min-h-12 flex-1" aria-live="polite">
                @foreach ($slides as $slide)
                    <div x-show="i === {{ $loop->index }}" x-transition.opacity.duration.500ms @if (! $loop->first) style="display: none" @endif>
                        <p class="font-mono text-[11px] tracking-[0.2em] text-white/60">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</p>
                        <p class="mt-1 truncate font-medium text-white">{{ $slide['caption'] }}@if ($slide['meta'])<span class="text-white/60"> — {{ $slide['meta'] }}</span>@endif</p>
                    </div>
                @endforeach
            </div>
            @if ($count > 1)
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-1">
                        @foreach ($slides as $slide)
                            <button type="button" @click="go({{ $loop->index }})" class="group flex h-11 items-center px-1" aria-label="Slide {{ $loop->iteration }}">
                                <span class="block h-1 rounded-full bg-white/40 transition-all duration-500 group-hover:bg-white/70" :class="i === {{ $loop->index }} ? 'w-10 !bg-white' : 'w-4'"></span>
                            </button>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="go(i - 1)" class="flex size-11 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white hover:text-black" aria-label="Slide sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                        <button type="button" @click="go(i + 1)" class="flex size-11 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white hover:text-black" aria-label="Slide berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
