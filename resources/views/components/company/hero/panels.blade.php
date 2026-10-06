{{-- Hero: Panels — 50/50 split screen: solid brand panel with headline & CTAs, full-height photograph. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 3);
@endphp
<section id="hero" class="relative grid overflow-hidden bg-surface lg:min-h-[92vh] lg:grid-cols-2">
    <div class="relative h-[55vh] min-h-80 lg:order-2 lg:h-auto">
        <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 size-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-b from-black/45 to-transparent to-40% lg:hidden"></div>
        @if ($company->services->isNotEmpty())
            <div class="absolute right-5 bottom-5 left-5 hidden max-w-sm rounded-brand bg-black/55 p-5 text-white backdrop-blur-md sm:block lg:right-auto lg:bottom-10 lg:left-10">
                <p class="text-[11px] font-semibold tracking-[0.2em] text-white/70 uppercase">Layanan utama</p>
                <p class="mt-1 font-semibold">{{ $company->services->first()->title }}</p>
            </div>
        @endif
    </div>

    <div class="tone-primary relative flex flex-col justify-center overflow-hidden bg-primary text-on-primary lg:order-1">
        <div class="pointer-events-none absolute -bottom-40 -left-40 size-[32rem] rounded-full border border-on-primary/15" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 size-[22rem] rounded-full border border-on-primary/10" aria-hidden="true"></div>
        <div class="relative w-full max-w-2xl px-5 pt-14 pb-16 sm:px-10 lg:ml-auto lg:px-14 lg:pt-36 lg:pb-20 xl:pr-20">
            <p class="text-xs font-semibold tracking-[0.22em] text-on-primary/75 uppercase" {!! $ds->reveal(0) !!}>
                {{ collect([$company->established_year ? 'Sejak '.$company->established_year : null, $company->city])->filter()->implode(' · ') ?: 'Profil Perusahaan' }}
            </p>
            <h1 class="heading mt-6 text-[min(var(--display),4.25rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-6 text-lead text-on-primary/80" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Layanan Kami', 'kind' => 'secondary'])
            </div>
            @if ($stats)
                <dl class="mt-14 grid grid-cols-3 gap-6 border-t border-on-primary/20 pt-8" {!! $ds->reveal(4) !!}>
                    @foreach ($stats as $stat)
                        <div>
                            <dd class="heading text-2xl sm:text-3xl" data-count>{{ $stat['value'] }}</dd>
                            <dt class="mt-1 text-xs text-on-primary/70">{{ $stat['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </div>
</section>
