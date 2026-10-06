{{-- Page header: Editorial — centered magazine opener: category line, giant title between thin rules, date line. --}}
<section class="bg-surface text-ink">
    <div class="{{ $ds->container('narrow') }} pt-14 pb-12 text-center sm:pt-20 sm:pb-16">
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[11px] font-semibold tracking-[0.22em] text-muted uppercase">
            <span class="hidden h-px flex-1 bg-line sm:block"></span>
            <a href="{{ $site->home() }}" class="transition hover:text-ink">{{ $company->name }}</a>
            <span aria-hidden="true" class="text-primary">&bull;</span>
            <span>Halaman</span>
            <span class="hidden h-px flex-1 bg-line sm:block"></span>
        </div>
        <h1 class="heading mx-auto mt-10 max-w-4xl text-[clamp(2.75rem,7.5vw,6.5rem)]" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
        @if ($page->seo_description)
            <p class="mx-auto mt-7 max-w-2xl font-heading text-xl leading-relaxed text-muted italic sm:text-2xl" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
        @endif
        <div class="mt-12 border-y border-line py-3 text-xs tracking-[0.16em] text-muted uppercase">
            @if ($page->updated_at)
                Diperbarui {{ $page->updated_at->translatedFormat('d F Y') }}
            @else
                {{ $company->name }}
            @endif
            @if ($company->city)<span class="mx-2 text-line">|</span>{{ $company->city }}@endif
        </div>
    </div>
</section>
