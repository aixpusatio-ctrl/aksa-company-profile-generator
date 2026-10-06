{{-- Hero: Portrait — tall arch/portrait photograph beside refined typography and a founder signature line. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $founder = $company->team->first();
@endphp
<section id="hero" class="relative overflow-hidden bg-surface">
    <div class="pointer-events-none absolute inset-y-0 left-0 w-[38%] bg-surface-alt max-lg:hidden" aria-hidden="true"></div>
    <div class="{{ $ds->container('wide') }} relative grid items-center gap-14 pt-28 pb-16 lg:grid-cols-12 lg:gap-16 lg:pt-36 lg:pb-24">
        <div class="relative order-2 mx-auto w-full max-w-sm sm:max-w-md lg:order-none lg:col-span-5 lg:max-w-none">
            <div class="absolute -inset-3 translate-x-5 translate-y-5 border border-primary/40 sm:-inset-4 {{ $ds->img() }}" aria-hidden="true"></div>
            <div class="relative overflow-hidden {{ $ds->img('shadow-2xl shadow-black/15') }}" {!! $ds->reveal(0) !!}>
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="aspect-[3/4] w-full object-cover transition duration-[1.6s] hover:scale-105" />
            </div>
            @if ($company->established_year)
                <div class="absolute -bottom-6 -left-2 flex size-24 flex-col items-center justify-center rounded-full bg-primary text-on-primary shadow-xl sm:-left-6 sm:size-28">
                    <span class="text-[10px] tracking-[0.25em] uppercase opacity-80">Est.</span>
                    <span class="heading text-xl sm:text-2xl">{{ $company->established_year }}</span>
                </div>
            @endif
        </div>

        <div class="lg:col-span-7 lg:pl-6">
            <div {!! $ds->reveal(1) !!}>{!! $ds->eyebrow($company->city ? $company->name.' — '.$company->city : $company->name) !!}</div>
            <h1 class="heading mt-7 text-[min(var(--display),4.75rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(2) !!}>{{ $title }}</h1>
            <div class="mt-8 h-px w-24 bg-primary" {!! $ds->reveal(2) !!}></div>
            @if ($lead)
                <p class="mt-8 max-w-xl text-lead text-muted" {!! $ds->reveal(3) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(4) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Jadwalkan Pertemuan', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('about'), 'label' => 'Tentang Kami', 'kind' => 'secondary'])
            </div>
            @if ($founder)
                <div class="mt-14 flex items-center gap-5 border-t border-line pt-8" {!! $ds->reveal(5) !!}>
                    <x-site.img :src="$founder->url('photo')" :alt="$founder->name" icon="user" class="size-14 shrink-0 rounded-full object-cover ring-2 ring-primary/30 ring-offset-2 ring-offset-surface" />
                    <div class="min-w-0">
                        <p class="truncate font-heading text-2xl text-ink italic">{{ $founder->name }}</p>
                        @if ($founder->position)<p class="mt-0.5 text-xs tracking-[0.18em] text-muted uppercase">{{ $founder->position }}</p>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
