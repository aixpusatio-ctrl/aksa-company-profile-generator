{{-- Hero: Bento — asymmetric tile grid: headline, photo, counters, service list and a CTA tile. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 2);
    $services = $company->services->take(4);
@endphp
<section id="hero" class="relative overflow-hidden bg-surface">
    <div class="{{ $ds->container('wide') }} pt-28 pb-16 lg:pt-32 lg:pb-24">
        <div class="grid gap-4 lg:grid-cols-12 lg:gap-5">
            {{-- Headline tile --}}
            <div class="{{ $ds->card('relative flex flex-col justify-between overflow-hidden p-7 sm:p-10 lg:col-span-7 lg:row-span-2 lg:p-12', false) }}" {!! $ds->reveal(0) !!}>
                <div class="pointer-events-none absolute -top-24 -right-24 size-72 rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>
                <div class="relative">
                    {!! $ds->eyebrow($company->established_year ? 'Sejak '.$company->established_year : 'Profil Perusahaan') !!}
                    <h1 class="heading mt-6 text-[min(var(--display),3.75rem)] max-sm:text-[min(var(--display),10vw)]">{{ $title }}</h1>
                    @if ($lead)
                        <p class="mt-6 max-w-xl text-lead text-muted">{{ $lead }}</p>
                    @endif
                </div>
                <div class="relative mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Konsultasi Gratis', 'kind' => 'primary'])
                    @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Lihat Layanan', 'kind' => 'secondary'])
                </div>
            </div>

            {{-- Image tile --}}
            <div class="relative min-h-72 overflow-hidden rounded-brand lg:col-span-5 lg:row-span-2" {!! $ds->reveal(1) !!}>
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 size-full object-cover transition duration-700 hover:scale-105" />
                @if ($company->city)
                    <span class="absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full bg-black/55 px-3 py-1.5 text-xs font-medium text-white backdrop-blur">
                        <x-icon name="map-pin" class="size-3.5" />{{ $company->city }}
                    </span>
                @endif
            </div>

            {{-- Stats tile --}}
            @if ($stats)
                <div class="tone-primary flex flex-col justify-between gap-8 rounded-brand bg-primary p-7 text-on-primary lg:col-span-4" {!! $ds->reveal(2) !!}>
                    <x-icon name="chart" class="size-6 opacity-80" />
                    <dl class="grid grid-cols-2 gap-6">
                        @foreach ($stats as $stat)
                            <div>
                                <dd class="heading text-3xl xl:text-4xl" data-count>{{ $stat['value'] }}</dd>
                                <dt class="mt-1 text-sm text-on-primary/75">{{ $stat['label'] }}</dt>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            {{-- Services tile --}}
            @if ($services->isNotEmpty())
                <div class="{{ $ds->card('p-7', false) }} {{ $stats ? 'lg:col-span-5' : 'lg:col-span-9' }}" {!! $ds->reveal(3) !!}>
                    <p class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Layanan Kami</p>
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($services as $service)
                            <li class="flex items-center gap-3 py-2.5">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-icon :name="$service->icon ?: 'sparkles'" class="size-4" /></span>
                                <span class="truncate text-sm font-medium text-ink">{{ $service->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CTA tile --}}
            <a href="{{ $site->anchor('contact') }}" class="group flex flex-col justify-between gap-8 rounded-brand bg-secondary p-7 text-on-secondary transition hover:-translate-y-1 {{ $stats || $services->isNotEmpty() ? 'lg:col-span-3' : 'lg:col-span-12' }}" {!! $ds->reveal(4) !!}>
                <span class="flex size-11 items-center justify-center rounded-full bg-on-secondary/15 transition group-hover:rotate-45">
                    <x-icon name="arrow-up-right" class="size-5" />
                </span>
                <span>
                    <span class="block text-sm opacity-75">Siap berkolaborasi?</span>
                    <span class="heading mt-1 block text-2xl">Hubungi Kami</span>
                </span>
            </a>
        </div>
    </div>
</section>
