{{-- Hero: Minimal — a single large statement, one quiet meta line and a link; generous whitespace, no image. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $meta = collect([$company->name, $company->city, $company->established_year ? 'Sejak '.$company->established_year : null])->filter();
@endphp
<section id="hero" class="relative bg-surface">
    <div class="{{ $ds->container() }} pt-40 pb-24 sm:pt-48 lg:pt-56 lg:pb-36">
        <p class="text-sm text-muted" {!! $ds->reveal(0) !!}>
            <span class="mr-3 inline-block h-px w-8 translate-y-[-4px] bg-ink"></span>{{ $company->name }}
        </p>
        <h1 class="heading mt-8 max-w-4xl text-[clamp(2.4rem,5.6vw,5rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        @if ($lead)
            <p class="mt-8 max-w-2xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
        @endif
        <div class="mt-16 flex flex-col gap-6 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between lg:mt-24" {!! $ds->reveal(3) !!}>
            <p class="font-mono text-xs tracking-[0.12em] text-muted uppercase">{{ $meta->implode('  /  ') }}</p>
            <div class="flex flex-wrap items-center gap-8">
                <a href="{{ $site->anchor('about') }}" class="text-sm font-medium text-muted transition hover:text-ink">Tentang kami</a>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai percakapan', 'kind' => 'link'])
            </div>
        </div>
    </div>
</section>
