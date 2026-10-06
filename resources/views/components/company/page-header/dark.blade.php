{{-- Page header: Dark — inverse band with a fading grid pattern, brand glow and a large title. --}}
<section class="{{ $ds->isDark() ? '' : 'tone-inverse' }} relative overflow-hidden border-b border-line bg-surface text-ink">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,var(--line)_1px,transparent_1px),linear-gradient(to_bottom,var(--line)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_80%_at_50%_0%,black,transparent)]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute -top-32 left-1/2 h-72 w-[min(48rem,100%)] -translate-x-1/2 rounded-full bg-primary/30 blur-[100px]"></div>
    <div class="{{ $ds->container() }} relative py-20 sm:py-28">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ $site->home() }}" class="transition hover:text-ink">Beranda</a>
            <x-icon name="chevron-right" class="size-3.5" />
            <span class="text-ink" aria-current="page">{{ $page->title }}</span>
        </nav>
        <h1 class="heading mt-8 max-w-4xl bg-linear-to-b from-ink to-ink/60 bg-clip-text pb-2 text-[clamp(2.5rem,6.5vw,5.5rem)] text-transparent" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
        @if ($page->seo_description)
            <p class="mt-6 max-w-2xl text-lead text-muted" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
        @endif
        <div class="mt-10 h-px w-full bg-linear-to-r from-primary/70 via-line to-transparent"></div>
    </div>
</section>
