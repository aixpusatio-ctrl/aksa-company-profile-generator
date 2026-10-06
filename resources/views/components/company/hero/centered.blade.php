{{-- Hero: Centered — big centered statement over glowing orbs & concentric rings, client names row. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $clients = array_slice($company->clients(), 0, 6);
    $pill = collect([$company->established_year ? 'Sejak '.$company->established_year : null, $company->city])->filter()->implode(' · ') ?: 'Profil Perusahaan';
    $glow = $ds->isDark() ? 55 : 28;
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    {{-- Decorative orbs & rings --}}
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="absolute top-[-14rem] left-1/2 size-[46rem] -translate-x-1/2 rounded-full blur-3xl"
            style="background: radial-gradient(circle at 35% 40%, color-mix(in oklab, var(--brand-primary) {{ $glow }}%, transparent), transparent 60%), radial-gradient(circle at 70% 60%, color-mix(in oklab, var(--brand-secondary) {{ $glow }}%, transparent), transparent 55%)"></div>
        <div class="absolute top-24 left-1/2 -translate-x-1/2">
            <div class="relative animate-float">
                <div class="size-[34rem] rounded-full border border-primary/15 sm:size-[46rem]"></div>
                <div class="absolute inset-16 rounded-full border border-primary/20"></div>
                <div class="absolute inset-32 rounded-full border border-secondary/20"></div>
                <div class="absolute top-[18%] left-[8%] size-3 rounded-full bg-primary shadow-[0_0_24px_6px] shadow-primary/60"></div>
                <div class="absolute right-[12%] bottom-[24%] size-2 rounded-full bg-secondary shadow-[0_0_20px_4px] shadow-secondary/60"></div>
            </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-surface to-transparent"></div>
    </div>

    <div class="{{ $ds->container('narrow') }} relative pt-36 pb-20 text-center lg:pt-44 lg:pb-28">
        <span class="inline-flex items-center gap-2 rounded-full border border-line bg-card/60 px-4 py-1.5 text-xs font-medium text-muted backdrop-blur" {!! $ds->reveal(0) !!}>
            <span class="relative flex size-2"><span class="absolute inline-flex size-full animate-ping rounded-full bg-primary opacity-60"></span><span class="relative inline-flex size-2 rounded-full bg-primary"></span></span>
            {{ $pill }}
        </span>
        <h1 class="heading mx-auto mt-8 max-w-4xl text-display max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        @if ($lead)
            <p class="mx-auto mt-6 max-w-2xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
        @endif
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row" {!! $ds->reveal(3) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Sekarang', 'kind' => 'primary'])
            @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Jelajahi Layanan', 'kind' => 'secondary'])
        </div>

        @if ($clients)
            <div class="mt-20" {!! $ds->reveal(4) !!}>
                <p class="text-xs font-semibold tracking-[0.22em] text-muted uppercase">Dipercaya oleh</p>
                <ul class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-4">
                    @foreach ($clients as $client)
                        <li class="heading text-base text-ink/55 transition hover:text-ink sm:text-lg">{{ $client }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
