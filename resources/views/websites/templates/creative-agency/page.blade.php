@extends('websites.templates.creative-agency.layout')

@section('content')
    <section class="relative overflow-hidden px-3 pt-6 sm:px-5">
        <div class="relative mx-auto max-w-[90rem] overflow-hidden rounded-[2rem] bg-primary px-6 py-20 text-on-primary sm:px-12 md:py-28">
            <nav class="flex items-center gap-2 text-sm font-bold">
                <a href="{{ $site->home() }}" class="rounded-full bg-black/10 px-3 py-1 hover:bg-black/20">Home</a>
                <x-icon name="arrow-right" class="size-4" />
                <span class="rounded-full bg-white px-3 py-1 text-neutral-950">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-8 font-heading text-6xl leading-[0.9] font-extrabold tracking-tighter md:text-8xl lg:text-9xl">{{ $page->title }}</h1>
            <span class="absolute right-8 bottom-8 hidden size-28 rotate-12 items-center justify-center rounded-full bg-neutral-950 text-center text-xs font-extrabold tracking-wider text-white uppercase md:inline-flex">Baca<br>sampai<br>habis ✦</span>
        </div>
    </section>
    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-3xl px-6 text-lg text-neutral-700">
            @include('websites.partials.page-content')
        </div>
    </section>
@endsection
