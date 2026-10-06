@extends('websites.templates.consulting.layout')

@section('content')
    <section class="border-b border-stone-200">
        <div class="mx-auto max-w-4xl px-6 py-20 text-center md:py-28">
            <nav class="flex items-center justify-center gap-3 text-xs tracking-[0.25em] text-stone-400 uppercase">
                <a href="{{ $site->home() }}" class="hover:text-stone-900">Beranda</a>
                <span class="h-px w-6 bg-stone-300"></span>
                <span class="text-primary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-8 font-heading text-5xl leading-tight text-stone-900 md:text-7xl">{{ $page->title }}</h1>
        </div>
    </section>
    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-2xl px-6 text-lg text-stone-700">
            @include('websites.partials.page-content')
            <div class="mt-16 border-t border-stone-200 pt-8 text-center">
                <p class="font-heading text-2xl text-stone-900 italic">Ada pertanyaan?</p>
                <a href="{{ $site->anchor('contact') }}" class="mt-4 inline-flex items-center gap-2 text-sm tracking-wide text-primary underline decoration-primary/30 underline-offset-8 hover:decoration-primary">Hubungi kami <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
    </section>
@endsection
