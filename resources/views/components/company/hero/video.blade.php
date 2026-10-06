{{-- Hero: Video — cinematic letterboxed showreel (looping file or Ken Burns still) with a play-in-modal button. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $videoType = $company->heroVideoType();
    $videoFile = $videoType === 'file' ? $company->url('hero_video') : null;
    $embed = in_array($videoType, ['youtube', 'vimeo'], true) ? $company->heroVideoEmbedUrl() : null;
    $canPlay = $videoFile || $embed;
@endphp
<section id="hero" class="relative isolate flex min-h-[94vh] flex-col overflow-hidden bg-black text-white"
    style="--ink:#fff;--muted:rgba(255,255,255,.78);--line:rgba(255,255,255,.3);--card:rgba(255,255,255,.08)"
    x-data="{ play: false }" @keydown.escape.window="play = false">
    <div class="absolute inset-0 -z-20 overflow-hidden">
        @if ($videoFile)
            <video class="size-full object-cover" autoplay muted loop playsinline preload="metadata" @if ($company->url('hero_image')) poster="{{ $company->url('hero_image') }}" @endif>
                <source src="{{ $videoFile }}">
            </video>
        @else
            <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="camera" class="size-full animate-ken-burns object-cover" />
        @endif
    </div>
    <div class="absolute inset-0 -z-10 bg-black/45"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black via-black/20 to-black/60"></div>
    <div class="absolute inset-0 -z-10" style="background: radial-gradient(ellipse at center, transparent 45%, rgba(0,0,0,.6) 100%)"></div>

    {{-- Letterbox bars --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[4vh] bg-black" aria-hidden="true"></div>

    <div class="{{ $ds->container('wide') }} relative flex flex-1 flex-col justify-end pt-36 pb-[calc(4vh+3.5rem)]">
        <div class="flex items-center gap-3 font-mono text-[11px] tracking-[0.3em] text-white/70 uppercase" {!! $ds->reveal(0) !!}>
            <span class="size-2 rounded-full bg-primary"></span>
            Showreel{{ $company->established_year ? ' · Sejak '.$company->established_year : '' }}
        </div>
        <h1 class="heading mt-6 max-w-5xl text-display text-white max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        <div class="mt-8 grid gap-10 lg:grid-cols-12 lg:items-end">
            @if ($lead)
                <p class="max-w-xl text-lead text-white/80 lg:col-span-6" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center lg:col-span-6 lg:justify-end" {!! $ds->reveal(3) !!}>
                @if ($canPlay)
                    <button type="button" @click="play = true" class="group inline-flex min-h-11 items-center gap-4 text-sm font-semibold text-white">
                        <span class="relative flex size-14 items-center justify-center rounded-full bg-white text-black transition group-hover:scale-110">
                            <span class="absolute inset-0 animate-ping rounded-full bg-white/40"></span>
                            <x-icon name="play" class="relative size-5 translate-x-px" />
                        </span>
                        Putar Showreel
                    </button>
                @endif
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Proyek', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Karya', 'kind' => 'secondary', 'class' => 'backdrop-blur'])
            </div>
        </div>
    </div>

    <div class="absolute inset-x-0 bottom-0 flex h-[4vh] min-h-8 items-center bg-black" aria-hidden="true">
        <div class="{{ $ds->container('wide') }} flex items-center justify-between font-mono text-[10px] tracking-[0.3em] text-white/50 uppercase">
            <span>{{ $company->name }}</span>
            <span class="hidden sm:inline">{{ $company->city ?: 'Profil Perusahaan' }}</span>
        </div>
    </div>

    @if ($canPlay)
        <div x-cloak x-show="play" x-transition.opacity class="fixed inset-0 z-[90] flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm" @click.self="play = false" role="dialog" aria-modal="true" aria-label="Showreel">
            <button type="button" @click="play = false" class="absolute top-4 right-4 flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20" aria-label="Tutup video">
                <x-icon name="x" class="size-6" />
            </button>
            <div class="aspect-video w-full max-w-5xl overflow-hidden rounded-brand bg-black shadow-2xl">
                <template x-if="play">
                    @if ($embed)
                        <iframe src="{{ $embed }}" class="size-full" title="Showreel {{ $company->name }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    @else
                        <video src="{{ $videoFile }}" class="size-full" controls autoplay playsinline></video>
                    @endif
                </template>
            </div>
        </div>
    @endif
</section>
