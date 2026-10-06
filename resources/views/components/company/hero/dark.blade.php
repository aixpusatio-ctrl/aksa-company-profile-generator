{{-- Hero: Dark — always-dark hero with huge type, masked grid lines, primary glow and a stats column. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 4);
@endphp
<section id="hero" class="{{ $ds->isDark() ? '' : 'tone-inverse' }} relative isolate overflow-hidden bg-surface text-ink">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="absolute inset-0"
            style="background-image: linear-gradient(to right, var(--line) 1px, transparent 1px), linear-gradient(to bottom, var(--line) 1px, transparent 1px); background-size: 72px 72px; -webkit-mask-image: radial-gradient(ellipse 80% 70% at 30% 40%, #000 30%, transparent 75%); mask-image: radial-gradient(ellipse 80% 70% at 30% 40%, #000 30%, transparent 75%)"></div>
        <div class="absolute -top-40 left-[10%] size-[38rem] rounded-full bg-primary/25 blur-[120px]"></div>
        <div class="absolute right-[-10rem] bottom-[-12rem] size-[30rem] rounded-full bg-secondary/15 blur-[120px]"></div>
    </div>

    <div class="{{ $ds->container('wide') }} relative grid gap-14 pt-36 pb-20 lg:grid-cols-12 lg:gap-10 lg:pt-44 lg:pb-28">
        <div class="lg:col-span-8">
            <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->established_year ? 'Sejak '.$company->established_year : ($company->city ?: 'Profil Perusahaan')) !!}</div>
            <h1 class="heading mt-7 text-[clamp(2.75rem,7vw,6.5rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-8 max-w-2xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Diskusikan Kebutuhan', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('about'), 'label' => 'Tentang Kami', 'kind' => 'secondary'])
            </div>
        </div>
        @if ($stats)
            <div class="lg:col-span-4 lg:self-end" {!! $ds->reveal(4, 'right') !!}>
                <dl class="divide-y divide-line border-y border-line">
                    @foreach ($stats as $stat)
                        <div class="flex items-baseline justify-between gap-6 py-5">
                            <dt class="text-sm text-muted">{{ $stat['label'] }}</dt>
                            <dd class="heading text-3xl text-ink lg:text-4xl" data-count>{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
                <p class="mt-5 flex items-center gap-2 font-mono text-[11px] tracking-[0.2em] text-muted uppercase">
                    <span class="size-1.5 rounded-full bg-primary"></span>{{ $company->name }}
                </p>
            </div>
        @endif
    </div>
</section>
