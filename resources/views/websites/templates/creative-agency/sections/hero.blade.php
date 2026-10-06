{{-- Creative hero: giant headline, rotated stickers, image card and an infinite marquee. --}}
@php
    $marquee = $company->services->pluck('title')->filter()->values();
    if ($marquee->isEmpty()) { $marquee = collect(['Branding', 'Strategi', 'Desain', 'Digital', 'Konten']); }
@endphp
<section id="hero" class="relative overflow-hidden pt-10 md:pt-16">
    <div class="mx-auto max-w-[90rem] px-6 sm:px-10">
        <div class="flex flex-wrap items-center gap-3 text-sm font-bold">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm"><span class="size-2 animate-pulse rounded-full bg-primary"></span> Tersedia untuk proyek baru</span>
            @if ($company->city)<span class="rounded-full border border-neutral-300 px-4 py-2 text-neutral-600">{{ $company->city }}</span>@endif
        </div>

        <h1 class="relative mt-8 font-heading text-[2.9rem] leading-[0.88] break-words font-extrabold tracking-tighter text-neutral-950 sm:text-7xl md:text-8xl lg:text-9xl xl:text-[9rem]">
            {{ $section->title ?: ($company->tagline ?: $company->name) }}
            <span class="absolute -top-6 right-0 hidden rotate-12 rounded-full bg-primary px-5 py-2 text-base font-extrabold tracking-normal text-on-primary shadow-xl md:inline-block">✦ Since {{ $company->established_year ?: date('Y') }}</span>
        </h1>

        <div class="mt-12 grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-4">
                <p class="text-lg leading-relaxed text-neutral-600 md:text-xl">{{ $section->subtitle ?: $company->description }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $site->anchor('projects') }}" class="group inline-flex items-center gap-3 rounded-btn bg-neutral-950 py-3 pr-3 pl-6 font-bold text-white transition hover:bg-primary hover:text-on-primary">
                        Lihat karya <span class="inline-flex size-8 items-center justify-center rounded-full bg-white/15 transition group-hover:rotate-45"><x-icon name="arrow-up-right" class="size-4" /></span>
                    </a>
                    <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center rounded-btn border-2 border-neutral-950 px-6 py-3 font-bold text-neutral-950 transition hover:-rotate-2">Mulai proyek</a>
                </div>
            </div>
            <div class="relative lg:col-span-8">
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="sparkles" class="aspect-[16/9] w-full rounded-brand object-cover" />
                <span class="absolute -top-8 left-6 inline-flex size-28 -rotate-12 items-center justify-center rounded-full bg-secondary text-center text-[11px] leading-tight font-extrabold tracking-widest text-on-secondary uppercase shadow-2xl md:size-36 md:text-xs">
                    <span>Award<br>winning<br>✦ studio ✦</span>
                </span>
                <span class="absolute right-5 -bottom-5 rotate-6 rounded-2xl bg-white px-5 py-3 font-heading text-lg font-extrabold text-neutral-950 shadow-xl">
                    {{ max($company->projects->count() * 20, 50) }}+ <span class="text-sm font-semibold text-neutral-500">proyek selesai</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Marquee --}}
    <div class="mt-20 -rotate-2 bg-primary py-5 text-on-primary md:mt-28">
        <div class="flex w-max animate-marquee">
            @foreach ([1, 2] as $copy)
                <div class="flex shrink-0 items-center" @if ($copy === 2) aria-hidden="true" @endif>
                    @foreach ($marquee as $word)
                        <span class="px-6 font-heading text-3xl font-extrabold tracking-tight uppercase md:text-5xl">{{ $word }}</span>
                        <span class="text-2xl md:text-4xl">✦</span>
                    @endforeach
                    @foreach ($marquee as $word)
                        <span class="px-6 font-heading text-3xl font-extrabold tracking-tight uppercase md:text-5xl">{{ $word }}</span>
                        <span class="text-2xl md:text-4xl">✦</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="h-10"></div>
</section>
