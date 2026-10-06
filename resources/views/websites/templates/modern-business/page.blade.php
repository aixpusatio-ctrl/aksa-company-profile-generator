@extends('websites.templates.modern-business.layout')

@section('content')
    <section class="relative overflow-hidden bg-linear-to-br from-primary via-primary to-secondary pt-36 pb-28 text-on-primary">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(255_255_255/0.18)_1px,transparent_0)] bg-[size:28px_28px] [mask-image:linear-gradient(to_bottom,black,transparent)]"></div>
        <div class="absolute -right-24 -bottom-32 size-96 rounded-full bg-white/15 blur-3xl"></div>
        <div class="relative mx-auto max-w-4xl px-5 text-center sm:px-6">
            <nav class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-medium text-on-primary/80 ring-1 ring-white/20 backdrop-blur">
                <a href="{{ $site->home() }}" class="hover:text-on-primary">Beranda</a>
                <x-icon name="chevron-right" class="size-3" />
                <span class="text-on-primary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-6 font-heading text-4xl font-semibold tracking-tight md:text-6xl">{{ $page->title }}</h1>
        </div>
    </section>
    <section class="relative -mt-16 pb-8">
        <div class="mx-auto max-w-4xl px-5 sm:px-6">
            <div class="rounded-brand bg-white p-6 text-slate-700 shadow-2xl shadow-slate-900/10 ring-1 ring-slate-100 sm:p-10 md:p-14">
                @include('websites.partials.page-content')
            </div>
        </div>
    </section>
@endsection
