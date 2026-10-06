@extends('websites.templates.technology.layout')

@section('content')
    <section class="relative overflow-hidden border-b border-white/10 pt-40 pb-20">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.05)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]"></div>
        <div class="absolute top-0 left-1/2 h-64 w-[36rem] max-w-full -translate-x-1/2 rounded-full bg-primary/25 blur-[100px]"></div>
        <div class="relative mx-auto max-w-4xl px-5 sm:px-6">
            <nav class="flex items-center gap-2 font-mono text-xs text-slate-500">
                <a href="{{ $site->home() }}" class="hover:text-primary">~/</a>
                <span>/</span>
                <span class="text-primary">{{ \Illuminate\Support\Str::slug($page->title) }}</span>
            </nav>
            <h1 class="mt-5 font-heading text-4xl font-bold tracking-tight text-white md:text-6xl">{{ $page->title }}</h1>
        </div>
    </section>
    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-3xl px-5 text-slate-300 sm:px-6 [&_.site-prose_h2]:text-white [&_.site-prose_h3]:text-white [&_.site-prose_strong]:text-white">
            @include('websites.partials.page-content')
        </div>
    </section>
@endsection
