{{-- Hero: Full Image — full-viewport photograph, dark gradient, headline bottom-left, scroll cue. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 3);
    $meta = collect([$company->city, $company->established_year ? 'Sejak '.$company->established_year : null])->filter()->implode(' · ') ?: 'Profil Perusahaan';
@endphp
<section id="hero" class="relative isolate flex min-h-[92vh] items-end overflow-hidden bg-black text-white"
    style="--ink:#fff;--muted:rgba(255,255,255,.78);--line:rgba(255,255,255,.32);--card:rgba(255,255,255,.08)">
    <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 -z-20 size-full object-cover" data-parallax="0.08" />
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/90 via-black/45 to-black/35"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/55 via-black/10 to-transparent"></div>

    <div class="{{ $ds->container('wide') }} relative pt-36 pb-14 lg:pb-20">
        <div class="grid items-end gap-12 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.25em] text-white/80 uppercase" {!! $ds->reveal(0) !!}>
                    <span class="h-px w-10 bg-white/60"></span>{{ $meta }}
                </p>
                <h1 class="heading mt-6 text-display text-white max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
                @if ($lead)
                    <p class="mt-6 max-w-2xl text-lead text-white/80" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
                @endif
                <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                    @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Proyek', 'kind' => 'secondary', 'class' => 'backdrop-blur'])
                </div>
            </div>
            @if ($stats)
                <dl class="grid grid-cols-3 gap-6 border-t border-white/25 pt-6 lg:col-span-4 lg:grid-cols-1 lg:gap-5 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8" {!! $ds->reveal(4) !!}>
                    @foreach ($stats as $stat)
                        <div>
                            <dd class="heading text-2xl text-white sm:text-3xl" data-count>{{ $stat['value'] }}</dd>
                            <dt class="mt-1 text-xs tracking-wide text-white/70">{{ $stat['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        <div class="mt-14 hidden items-center gap-4 text-[11px] font-semibold tracking-[0.3em] text-white/70 uppercase sm:flex" aria-hidden="true">
            <span class="relative flex h-10 w-6 justify-center rounded-full border border-white/50">
                <span class="mt-2 h-2 w-1 animate-bounce rounded-full bg-white"></span>
            </span>
            Gulir untuk menjelajah
        </div>
    </div>
</section>
