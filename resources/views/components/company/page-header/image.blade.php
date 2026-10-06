{{-- Page header: Image — full-width featured image (or company hero image) with a dark overlay and the title anchored bottom-left. --}}
@php($image = $page->url('featured_image') ?: $company->url('hero_image'))
<section class="relative isolate flex min-h-[22rem] items-end overflow-hidden bg-secondary text-white sm:min-h-[28rem] lg:min-h-[32rem]">
    <x-site.img :src="$image" alt="" class="animate-ken-burns absolute inset-0 -z-20 size-full object-cover" />
    <div aria-hidden="true" class="absolute inset-0 -z-10 bg-linear-to-t from-black/85 via-black/45 to-black/25"></div>
    <div class="{{ $ds->container() }} pt-28 pb-12 sm:pb-16">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-white/70" aria-label="Breadcrumb">
            <a href="{{ $site->home() }}" class="transition hover:text-white">Beranda</a>
            <x-icon name="chevron-right" class="size-3.5" />
            <span class="text-white" aria-current="page">{{ $page->title }}</span>
        </nav>
        <h1 class="heading mt-5 max-w-4xl text-[clamp(2.5rem,6vw,5rem)] text-white" {!! $ds->reveal() !!}>{{ $page->title }}</h1>
        @if ($page->seo_description)
            <p class="mt-5 max-w-2xl text-lead text-white/80" {!! $ds->reveal(1) !!}>{{ $page->seo_description }}</p>
        @endif
        <div class="mt-8 h-1 w-16 bg-primary"></div>
    </div>
</section>
