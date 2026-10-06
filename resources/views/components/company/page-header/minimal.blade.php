{{-- Page header: Minimal — breadcrumb, large left-aligned title and description with generous whitespace and a bottom hairline. --}}
<section class="border-b border-line bg-surface text-ink">
    <div class="{{ $ds->container() }} pt-16 pb-14 sm:pt-24 sm:pb-20">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ $site->home() }}" class="transition hover:text-ink">Beranda</a>
            <span aria-hidden="true" class="text-line">/</span>
            <span class="text-ink" aria-current="page">{{ $page->title }}</span>
        </nav>
        <h1 class="heading mt-8 max-w-5xl text-[clamp(2.5rem,6.5vw,5.5rem)]" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
        @if ($page->seo_description)
            <p class="mt-6 max-w-2xl text-lead text-muted" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
        @endif
    </div>
</section>
