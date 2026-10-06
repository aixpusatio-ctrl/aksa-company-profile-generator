@extends('websites.templates.professional-services.layout')

@section('content')
    <section class="border-b border-slate-200 bg-secondary/10">
        <div class="mx-auto max-w-7xl px-6 py-14 md:py-20">
            <nav class="flex items-center gap-2 text-xs font-medium tracking-wide text-slate-500 uppercase">
                <a href="{{ $site->home() }}" class="hover:text-primary">Beranda</a>
                <x-icon name="chevron-right" class="size-3" />
                <span class="text-primary">{{ $page->title }}</span>
            </nav>
            <h1 class="mt-5 max-w-3xl font-heading text-3xl leading-tight text-slate-900 md:text-5xl">{{ $page->title }}</h1>
            <div class="mt-6 h-0.5 w-16 bg-secondary"></div>
        </div>
    </section>

    <section class="py-16 md:py-20">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-12">
            <article class="text-slate-700 lg:col-span-8">
                @include('websites.partials.page-content')
            </article>
            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-brand bg-primary p-7 text-on-primary">
                        <x-icon name="calendar" class="size-7 text-secondary" />
                        <p class="mt-4 font-heading text-xl">Jadwalkan konsultasi</p>
                        <p class="mt-2 text-sm text-on-primary/75">Diskusikan kebutuhan Anda secara rahasia dengan tim profesional kami.</p>
                        <a href="{{ $site->anchor('contact') }}" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-btn bg-secondary px-5 py-3 text-sm font-semibold text-on-secondary">Buat Janji <x-icon name="arrow-right" class="size-4" /></a>
                    </div>
                    @if ($pages->count() > 1)
                        <div class="rounded-brand border border-slate-200 p-6">
                            <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Halaman lain</p>
                            <ul class="mt-4 divide-y divide-slate-100 text-sm">
                                @foreach ($pages as $p)
                                    <li><a href="{{ $site->page($p->slug) }}" class="flex items-center justify-between py-2.5 {{ $p->slug === $page->slug ? 'font-semibold text-primary' : 'text-slate-700 hover:text-primary' }}">{{ $p->title }} <x-icon name="chevron-right" class="size-3.5 opacity-50" /></a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
