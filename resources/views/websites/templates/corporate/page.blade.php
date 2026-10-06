@extends('websites.templates.corporate.layout')

@section('content')
    <section class="relative overflow-hidden bg-secondary py-20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,var(--brand-primary),transparent_55%)] opacity-40"></div>
        <div class="relative mx-auto max-w-7xl px-6">
            <nav class="flex items-center gap-2 text-sm text-on-secondary/60">
                <a href="{{ $site->home() }}" class="hover:text-on-secondary">Home</a>
                <x-icon name="chevron-right" class="size-3.5" />
                <span class="text-on-secondary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-4 font-heading text-4xl font-extrabold text-on-secondary md:text-5xl">{{ $page->title }}</h1>
        </div>
    </section>
    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-3xl px-6 text-slate-700">
            @include('websites.partials.page-content')
        </div>
    </section>
@endsection
