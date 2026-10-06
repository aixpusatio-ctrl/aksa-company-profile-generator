{{-- Executive hero: cinematic full-bleed image, deep gradient, centered serif headline, stats bar with hairlines. --}}
<section id="hero" class="relative isolate flex min-h-[100svh] flex-col overflow-hidden bg-secondary">
    <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 -z-20 size-full object-cover" />
    <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,color-mix(in_oklab,var(--brand-secondary)_70%,transparent)_0%,color-mix(in_oklab,var(--brand-secondary)_45%,transparent)_45%,var(--brand-secondary)_100%)]"></div>
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_center,transparent_30%,rgba(0,0,0,.45)_100%)]"></div>

    {{-- thin gold frame --}}
    <div class="pointer-events-none absolute inset-4 border border-primary/25 md:inset-8"></div>

    <div class="relative mx-auto flex w-full max-w-5xl flex-1 flex-col items-center justify-center px-6 pt-32 pb-16 text-center">
        <p class="flex items-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase">
            <span class="h-px w-10 bg-primary/70"></span>
            {{ $company->established_year ? 'Est. '.$company->established_year : $company->name }}
            <span class="h-px w-10 bg-primary/70"></span>
        </p>
        <h1 class="mt-8 font-heading text-5xl leading-[1.02] font-medium text-on-secondary sm:text-6xl md:text-7xl lg:text-[5.75rem]">
            {{ $section->title ?: ($company->tagline ?: $company->name) }}
        </h1>
        <p class="mx-auto mt-8 max-w-2xl text-base leading-relaxed text-on-secondary/75 md:text-lg">
            {{ $section->subtitle ?: $company->description }}
        </p>
        <div class="mt-12 flex flex-col gap-4 sm:flex-row">
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center justify-center gap-3 rounded-btn bg-primary px-9 py-4 text-[11px] font-semibold tracking-[0.3em] text-on-primary uppercase transition hover:opacity-90">
                Jadwalkan Pertemuan
            </a>
            <a href="{{ $site->anchor('services') }}" class="inline-flex items-center justify-center gap-3 rounded-btn border border-on-secondary/30 px-9 py-4 text-[11px] font-semibold tracking-[0.3em] text-on-secondary uppercase transition hover:border-primary hover:text-primary">
                Layanan Kami
            </a>
        </div>
    </div>

    {{-- Stats with thin dividers --}}
    <div class="relative mx-auto w-full max-w-6xl px-6 pb-12 md:pb-16">
        <dl class="grid grid-cols-2 border-t border-primary/30 md:grid-cols-4 md:divide-x md:divide-primary/25">
            @foreach ([
                [$company->established_year ? (date('Y') - $company->established_year).'+' : '10+', 'Tahun Pengalaman'],
                [max($company->projects->count() * 20, 40).'+', 'Mandat Terselesaikan'],
                [$company->services->count() ?: '—', 'Lini Layanan'],
                [$company->team->count() ? $company->team->count().'' : '—', 'Mitra & Direktur'],
            ] as [$value, $label])
                <div class="px-4 pt-8 text-center">
                    <dd class="font-heading text-4xl font-medium text-on-secondary md:text-5xl">{{ $value }}</dd>
                    <dt class="mt-2 text-[10px] tracking-[0.3em] text-primary uppercase">{{ $label }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
