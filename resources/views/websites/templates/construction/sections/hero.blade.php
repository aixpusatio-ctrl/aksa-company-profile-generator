{{-- Construction hero: full-height photo with dark overlay, huge uppercase headline, diagonal primary block, hazard stripes + big stats bar. --}}
@php
    $years = $company->established_year ? max(date('Y') - $company->established_year, 1) : null;
    $stats = array_values(array_filter([
        $years ? [$years, '+', 'Tahun Pengalaman'] : null,
        $company->projects->count() ? [$company->projects->count(), '', 'Proyek Unggulan'] : null,
        $company->services->count() ? [$company->services->count(), '', 'Lini Layanan'] : null,
        $company->team->count() ? [$company->team->count(), '', 'Tenaga Ahli Inti'] : null,
    ]));
@endphp
<section id="hero" class="relative">
    <div class="relative flex min-h-[calc(100svh-5rem)] items-center overflow-hidden bg-stone-950 md:min-h-[calc(100svh-7.5rem)]">
        <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 size-full object-cover" />
        <div class="absolute inset-0 bg-stone-950/60"></div>
        <div class="absolute inset-0 bg-linear-to-r from-stone-950 via-stone-950/75 to-transparent"></div>

        {{-- diagonal accents --}}
        <div class="absolute top-0 right-0 hidden h-full w-[28%] bg-primary [clip-path:polygon(45%_0,100%_0,100%_100%,0_100%)] opacity-90 lg:block"></div>
        <div class="absolute top-0 right-[22%] hidden h-full w-24 bg-white/5 [clip-path:polygon(70%_0,100%_0,30%_100%,0_100%)] lg:block"></div>

        <div class="relative mx-auto w-full max-w-7xl px-5 py-24 sm:px-6 lg:py-32">
            <div class="max-w-4xl">
                <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase">
                    <span class="h-1 w-12 bg-primary"></span>
                    {{ $company->established_year ? 'Sejak '.$company->established_year : 'Kontraktor Profesional' }}{{ $company->city ? ' · '.$company->city : '' }}
                </p>
                <h1 class="mt-7 font-heading text-5xl leading-[0.92] font-bold tracking-tight text-white uppercase sm:text-7xl lg:text-8xl xl:text-[7rem]">
                    {{ $section->title ?: ($company->tagline ?: $company->name) }}
                </h1>
                <p class="mt-8 max-w-2xl border-l-4 border-primary pl-6 text-base leading-relaxed text-stone-300 sm:text-lg">
                    {{ $section->subtitle ?: $company->description }}
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ $site->anchor('contact') }}" class="group inline-flex items-center gap-4 rounded-btn bg-primary px-8 py-4 font-heading text-sm font-bold tracking-widest text-on-primary uppercase transition hover:brightness-110">
                        Minta Penawaran
                        <x-icon name="arrow-right" class="size-5 transition group-hover:translate-x-1" />
                    </a>
                    <a href="{{ $site->anchor('projects') }}" class="inline-flex items-center gap-3 rounded-btn border-2 border-white/80 px-8 py-4 font-heading text-sm font-bold tracking-widest text-white uppercase transition hover:bg-white hover:text-stone-950">
                        Lihat Proyek
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 h-3 bg-[repeating-linear-gradient(-45deg,var(--brand-primary)_0_14px,#0c0a09_14px_28px)]"></div>
    </div>

    {{-- Big numeric stats bar --}}
    @if (count($stats))
        <div class="bg-stone-950">
            <div class="mx-auto grid max-w-7xl grid-cols-2 px-5 sm:px-6 lg:grid-cols-4">
                @foreach ($stats as [$value, $suffix, $label])
                    <div class="border-white/10 py-10 lg:py-14 {{ $loop->index % 2 === 0 ? 'pr-4' : 'border-l pl-6' }} lg:border-l lg:px-8 lg:first:border-l-0 lg:first:pl-0 {{ $loop->index < 2 ? 'max-lg:border-b' : '' }}">
                        <p class="font-heading text-5xl leading-none font-bold text-white sm:text-6xl lg:text-7xl">{{ $value }}<span class="text-primary">{{ $suffix ?: '.' }}</span></p>
                        <p class="mt-3 font-heading text-xs font-semibold tracking-[0.25em] text-stone-400 uppercase sm:text-sm">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
