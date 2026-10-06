@extends('websites.templates.executive.layout')

@section('content')
    <section class="relative overflow-hidden bg-secondary pt-40 pb-20 text-center md:pt-48 md:pb-28">
        @if ($page->featured_image || $company->hero_image)
            <x-site.img :src="$company->url('hero_image')" alt="" class="absolute inset-0 size-full object-cover opacity-20" />
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-secondary/60 via-secondary/80 to-secondary"></div>
        <div class="relative mx-auto max-w-4xl px-6">
            <nav class="flex items-center justify-center gap-3 text-[11px] tracking-[0.3em] text-on-secondary/50 uppercase">
                <a href="{{ $site->home() }}" class="hover:text-primary">Beranda</a>
                <span class="h-px w-6 bg-primary/60"></span>
                <span class="text-primary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-8 font-heading text-5xl leading-[1.05] font-medium text-on-secondary md:text-7xl">{{ $page->title }}</h1>
            <div class="mx-auto mt-10 flex max-w-xs items-center gap-4">
                <span class="h-px flex-1 bg-gradient-to-r from-transparent to-primary"></span>
                <span class="size-1.5 rotate-45 bg-primary"></span>
                <span class="h-px flex-1 bg-gradient-to-l from-transparent to-primary"></span>
            </div>
        </div>
    </section>
    <section class="bg-stone-50 py-20 md:py-28">
        <div class="mx-auto max-w-3xl px-6 text-[17px] text-slate-700 [&_.site-prose_h2]:text-3xl [&_.site-prose_h2]:font-medium [&_.site-prose_h2]:text-secondary [&_.site-prose_h3]:text-secondary">
            @include('websites.partials.page-content')
        </div>
    </section>
@endsection
