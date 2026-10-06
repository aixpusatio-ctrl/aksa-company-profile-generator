{{-- Page header: Banner — tinted band with breadcrumb and title. --}}
<section class="relative overflow-hidden border-b border-line bg-surface-alt">
    <div class="absolute -top-24 right-0 size-80 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="{{ $ds->container() }} relative py-16 sm:py-20">
        <nav class="flex items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ $site->home() }}" class="hover:text-ink">Beranda</a>
            <x-icon name="chevron-right" class="size-3.5" />
            <span class="text-ink">{{ $page->title }}</span>
        </nav>
        <h1 class="heading mt-5 text-h2" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
        @if ($page->seo_description)
            <p class="mt-4 max-w-2xl text-lead text-muted" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
        @endif
    </div>
</section>
