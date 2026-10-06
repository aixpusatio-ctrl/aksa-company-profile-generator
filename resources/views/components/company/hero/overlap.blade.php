{{-- Hero: Overlap — full-width image band with a large content card overlapping its bottom edge. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 4);
@endphp
<section id="hero" class="relative bg-surface pb-16 lg:pb-24">
    <div class="relative h-[62vh] min-h-[26rem] overflow-hidden lg:h-[72vh]">
        <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="size-full object-cover" data-parallax="0.12" />
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/10 to-black/30"></div>
        <div class="{{ $ds->container() }} absolute inset-x-0 top-0 pt-32 lg:pt-36">
            <p class="inline-flex items-center gap-2 rounded-full bg-black/35 px-4 py-1.5 text-xs font-semibold tracking-[0.18em] text-white uppercase backdrop-blur" {!! $ds->reveal(0) !!}>
                <x-icon name="map-pin" class="size-3.5" />{{ collect([$company->city, $company->established_year ? 'Sejak '.$company->established_year : null])->filter()->implode(' · ') ?: $company->name }}
            </p>
        </div>
    </div>

    <div class="{{ $ds->container() }} relative z-10 -mt-40 sm:-mt-48 lg:-mt-56">
        <div class="rounded-[calc(var(--brand-radius)*1.5)] bg-card p-6 shadow-2xl shadow-black/15 ring-1 ring-line max-sm:px-5 sm:p-10 lg:p-14" {!! $ds->reveal(1) !!}>
            <div class="grid gap-8 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-7">
                    {!! $ds->eyebrow($company->name) !!}
                    <h1 class="heading mt-5 text-[min(var(--display),3.75rem)] max-sm:text-[min(var(--display),9vw)]">{{ $title }}</h1>
                </div>
                <div class="flex flex-col justify-end lg:col-span-5">
                    @if ($lead)<p class="text-lead text-muted">{{ $lead }}</p>@endif
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                        @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Program & Layanan', 'kind' => 'secondary'])
                    </div>
                </div>
            </div>
            @if ($stats)
                <dl class="mt-10 grid grid-cols-2 gap-px overflow-hidden rounded-brand bg-line ring-1 ring-line lg:mt-14 {{ count($stats) >= 4 ? 'md:grid-cols-4' : (count($stats) === 3 ? 'md:grid-cols-3' : '') }}">
                    @foreach ($stats as $stat)
                        <div class="bg-card p-5 sm:p-6">
                            <dd class="heading text-3xl text-primary" data-count>{{ $stat['value'] }}</dd>
                            <dt class="mt-1 text-sm text-muted">{{ $stat['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </div>
</section>
