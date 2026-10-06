{{-- Hero: Editorial — magazine cover: meta rule, giant full-width headline, captioned image, asymmetric columns. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $services = $company->services->take(4);
@endphp
<section id="hero" class="relative overflow-hidden bg-surface">
    <div class="{{ $ds->container('wide') }} pt-28 pb-16 lg:pt-32 lg:pb-24">
        <div class="flex flex-wrap items-center justify-between gap-x-8 gap-y-2 border-y border-ink py-3 text-[11px] font-semibold tracking-[0.22em] text-ink uppercase" {!! $ds->reveal(0) !!}>
            <span>{{ $company->established_year ? 'Est. '.$company->established_year : $company->name }}</span>
            @if ($company->city)<span class="hidden sm:inline">{{ $company->city }}</span>@endif
            <span>Profil Perusahaan</span>
            <span class="hidden md:inline">Edisi {{ date('Y') }}</span>
        </div>

        <h1 class="heading mt-8 text-[clamp(3rem,10vw,9rem)] lg:mt-10 max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>

        <div class="mt-10 grid gap-10 border-t border-line pt-10 lg:mt-14 lg:grid-cols-12 lg:gap-12">
            <div class="flex flex-col lg:col-span-4" {!! $ds->reveal(2) !!}>
                @if ($lead)
                    <p class="text-lead text-ink first-letter:float-left first-letter:mr-3 first-letter:font-heading first-letter:text-6xl first-letter:leading-[0.8] first-letter:text-primary">{{ $lead }}</p>
                @endif
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Kolaborasi', 'kind' => 'primary'])
                    @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Karya', 'kind' => 'link'])
                </div>
                @if ($services->isNotEmpty())
                    <ol class="mt-12 divide-y divide-line border-t border-line lg:mt-auto">
                        @foreach ($services as $service)
                            <li class="flex items-baseline gap-4 py-3 text-sm">
                                <span class="font-mono text-xs text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="font-medium text-ink">{{ $service->title }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
            <figure class="lg:col-span-8" {!! $ds->reveal(3) !!}>
                <div class="overflow-hidden {{ $ds->img() }}">
                    <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="photo" class="aspect-[4/3] w-full object-cover transition duration-[1.5s] hover:scale-105 sm:aspect-[16/10]" />
                </div>
                <figcaption class="mt-3 flex items-baseline justify-between gap-6 text-xs text-muted">
                    <span><span class="font-mono text-primary">Fig. 01</span> — {{ $company->name }}{{ $company->city ? ', '.$company->city : '' }}</span>
                    @if ($company->established_year)<span class="hidden sm:inline">{{ $company->established_year }} — {{ date('Y') }}</span>@endif
                </figcaption>
            </figure>
        </div>
    </div>
</section>
