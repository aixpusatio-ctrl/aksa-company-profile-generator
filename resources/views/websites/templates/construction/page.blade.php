@extends('websites.templates.construction.layout')

@section('content')
    <section class="relative overflow-hidden bg-stone-900">
        <x-site.img :src="$page->featured_image ? $page->url('featured_image') : $company->url('hero_image')" alt="" icon="building" class="absolute inset-0 size-full object-cover opacity-30 grayscale" />
        <div class="absolute inset-0 bg-linear-to-r from-stone-950 via-stone-950/80 to-stone-950/30"></div>
        <div class="absolute top-0 right-0 hidden h-full w-1/3 bg-primary/90 [clip-path:polygon(35%_0,100%_0,100%_100%,0_100%)] lg:block"></div>
        <div class="relative mx-auto max-w-7xl px-5 py-20 sm:px-6 lg:py-28">
            <nav class="flex items-center gap-3 font-heading text-sm font-semibold tracking-widest text-stone-400 uppercase">
                <a href="{{ $site->home() }}" class="hover:text-primary">Beranda</a>
                <span class="h-0.5 w-6 bg-primary"></span>
                <span class="text-primary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-5 max-w-3xl font-heading text-5xl leading-[0.95] font-bold tracking-tight text-white uppercase md:text-7xl">{{ $page->title }}</h1>
        </div>
        <div class="relative h-3 bg-[repeating-linear-gradient(-45deg,var(--brand-primary)_0_14px,#0c0a09_14px_28px)]"></div>
    </section>
    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-3xl border-l-4 border-primary px-5 text-stone-700 sm:px-8 [&_.site-prose_h2]:uppercase [&_.site-prose_h2]:text-stone-950">
            @include('websites.partials.page-content')
        </div>
    </section>
@endsection
