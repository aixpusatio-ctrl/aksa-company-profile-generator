{{-- Hero: Cinematic — full-height dark photograph with heavy vignette, centered light type, thin rules and small caps. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 3);
@endphp
<section id="hero" class="relative isolate flex min-h-screen flex-col overflow-hidden bg-black text-white"
    style="--ink:#fff;--muted:rgba(255,255,255,.75);--line:rgba(255,255,255,.3);--card:rgba(255,255,255,.06)">
    <div class="absolute inset-0 -z-20 overflow-hidden">
        <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="size-full animate-ken-burns object-cover opacity-70" />
    </div>
    <div class="absolute inset-0 -z-10" style="background: radial-gradient(ellipse 70% 60% at 50% 45%, rgba(0,0,0,.25) 0%, rgba(0,0,0,.65) 60%, rgba(0,0,0,.95) 100%)"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-black/50 via-transparent to-black/80"></div>

    <div class="{{ $ds->container('wide') }} flex flex-1 flex-col items-center justify-center pt-36 pb-12 text-center">
        <div class="flex items-center gap-4 text-[11px] tracking-[0.4em] text-white/75 uppercase" {!! $ds->reveal(0) !!}>
            <span class="h-px w-10 bg-white/40 sm:w-16"></span>
            <span>{{ collect([$company->city ?: $company->name, $company->established_year ? 'Est. '.$company->established_year : null])->filter()->implode(' · ') }}</span>
            <span class="h-px w-10 bg-white/40 sm:w-16"></span>
        </div>
        <h1 class="heading mt-8 max-w-5xl text-[clamp(2.75rem,7.5vw,7rem)] text-white max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        @if ($lead)
            <p class="mt-8 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
        @endif
        <div class="mt-12 flex flex-col items-center gap-4 sm:flex-row" {!! $ds->reveal(3) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Reservasi Konsultasi', 'kind' => 'light'])
            @include('components.company.partials.button', ['href' => $site->anchor('about'), 'label' => 'Kisah Kami', 'kind' => 'link', 'class' => '!text-white'])
        </div>
    </div>

    <div class="{{ $ds->container('wide') }} pb-8">
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-3 border-t border-white/20 pt-6 text-[11px] tracking-[0.3em] text-white/60 uppercase sm:justify-between" {!! $ds->reveal(4) !!}>
            <span>{{ $company->name }}</span>
            @foreach ($stats as $stat)
                <span class="hidden sm:inline"><span data-count class="text-white">{{ $stat['value'] }}</span> {{ $stat['label'] }}</span>
            @endforeach
            <span class="hidden md:inline">Profil Perusahaan</span>
        </div>
    </div>
</section>
