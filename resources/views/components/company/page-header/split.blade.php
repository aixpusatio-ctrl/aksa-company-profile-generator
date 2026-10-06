{{-- Page header: Split — breadcrumb, title and description on the left, featured image (or company hero image) on the right. --}}
@php($image = $page->url('featured_image') ?: $company->url('hero_image'))
<section class="relative overflow-hidden border-b border-line bg-surface-alt text-ink">
    <div class="{{ $ds->container() }} grid items-center gap-10 py-14 sm:py-20 lg:grid-cols-2 lg:gap-16">
        <div>
            <nav class="flex flex-wrap items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ $site->home() }}" class="transition hover:text-ink">Beranda</a>
                <x-icon name="chevron-right" class="size-3.5" />
                <span class="text-ink" aria-current="page">{{ $page->title }}</span>
            </nav>
            <div class="mt-8">{!! $ds->eyebrow($company->name) !!}</div>
            <h1 class="heading mt-4 text-[clamp(2.25rem,5vw,4.25rem)]" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
            @if ($page->seo_description)
                <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
            @endif
            @if ($page->updated_at)
                <p class="mt-8 inline-flex items-center gap-2 text-xs text-muted"><x-icon name="calendar" class="size-4" /> Diperbarui {{ $page->updated_at->translatedFormat('d F Y') }}</p>
            @endif
        </div>
        <div class="relative" {!! $ds->reveal(2, 'right') !!}>
            <div aria-hidden="true" class="absolute -inset-4 -z-0 rounded-[calc(var(--brand-radius)*2)] bg-primary/10 sm:-inset-6"></div>
            <x-site.img :src="$image" :alt="$page->title" class="{{ $ds->img('relative aspect-[4/3] w-full object-cover shadow-[0_30px_60px_-30px_rgb(0_0_0/0.4)]') }}" />
        </div>
    </div>
</section>
