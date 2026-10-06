{{-- Hero: Collage — text left, overlapping framed photo collage (hero, gallery & project images) right. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $images = collect([['src' => $company->url('hero_image'), 'alt' => $company->name]])
        ->merge($company->gallery->map(fn ($g) => ['src' => $g->url('image'), 'alt' => $g->title ?: $company->name]))
        ->merge($company->projects->map(fn ($p) => ['src' => $p->url('image'), 'alt' => $p->title]))
        ->filter(fn ($img) => filled($img['src']))
        ->unique('src')
        ->values();
    $img = fn (int $i) => $images[$i] ?? ['src' => null, 'alt' => $company->name];
    $frame = 'ring-4 sm:ring-8 ring-surface shadow-2xl shadow-black/15';
@endphp
<section id="hero" class="relative overflow-hidden bg-surface">
    <div class="pointer-events-none absolute top-0 right-0 h-full w-1/2 bg-surface-alt max-lg:hidden" aria-hidden="true"></div>
    <div class="{{ $ds->container('wide') }} relative grid items-center gap-14 pt-28 pb-16 lg:grid-cols-12 lg:gap-10 lg:pt-36 lg:pb-24">
        <div class="lg:col-span-6">
            <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->city ? 'Berbasis di '.$company->city : 'Profil Perusahaan') !!}</div>
            <h1 class="heading mt-6 text-[min(var(--display),4rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('gallery'), 'label' => 'Lihat Galeri', 'kind' => 'secondary'])
            </div>
        </div>

        <div class="relative h-[26rem] sm:h-[34rem] lg:col-span-6 lg:h-[40rem]" aria-label="Kolase foto {{ $company->name }}">
            <div class="absolute top-0 left-0 h-[68%] w-[64%] overflow-hidden {{ $ds->img($frame) }}" {!! $ds->reveal(1) !!}>
                <x-site.img :src="$img(0)['src']" :alt="$img(0)['alt']" icon="building" class="size-full object-cover" />
            </div>
            <div class="absolute top-[8%] right-0 h-[44%] w-[42%] overflow-hidden {{ $ds->img($frame) }}" {!! $ds->reveal(2) !!} data-parallax="0.06">
                <x-site.img :src="$img(1)['src']" :alt="$img(1)['alt']" icon="photo" class="size-full object-cover" />
            </div>
            <div class="absolute bottom-0 left-[14%] h-[40%] w-[46%] overflow-hidden {{ $ds->img($frame) }}" {!! $ds->reveal(3) !!}>
                <x-site.img :src="$img(2)['src']" :alt="$img(2)['alt']" icon="photo" class="size-full object-cover" />
            </div>
            <div class="absolute right-[4%] bottom-[6%] h-[36%] w-[34%] overflow-hidden {{ $ds->img($frame) }}" {!! $ds->reveal(4) !!} data-parallax="0.1">
                <x-site.img :src="$img(3)['src']" :alt="$img(3)['alt']" icon="photo" class="size-full object-cover" />
            </div>
            @if ($company->established_year)
                <div class="absolute top-[56%] left-[56%] hidden size-28 -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full bg-primary text-center text-on-primary shadow-xl sm:flex" {!! $ds->reveal(5) !!}>
                    <span class="text-[10px] font-semibold tracking-[0.2em] uppercase opacity-80">Sejak</span>
                    <span class="heading text-2xl">{{ $company->established_year }}</span>
                </div>
            @endif
        </div>
    </div>
</section>
