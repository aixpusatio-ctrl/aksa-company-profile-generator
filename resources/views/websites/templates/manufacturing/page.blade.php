@extends('websites.templates.manufacturing.layout')

@section('content')
    <section class="relative border-b border-slate-200 bg-slate-50">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(15_23_42/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42/0.05)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
        <div class="relative mx-auto max-w-7xl px-6 py-14 md:py-20">
            <nav class="flex items-center gap-2 font-mono text-xs tracking-wider text-slate-500 uppercase">
                <a href="{{ $site->home() }}" class="hover:text-primary">Beranda</a>
                <span class="text-primary">/</span>
                <span class="text-slate-800">{{ $page->title }}</span>
            </nav>
            <div class="mt-5 flex items-end gap-5">
                <span class="hidden h-14 w-1.5 bg-primary md:block"></span>
                <h1 class="font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-5xl">{{ $page->title }}</h1>
            </div>
        </div>
    </section>
    <section class="py-14 md:py-20">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-12">
            <div class="text-slate-700 lg:col-span-8">
                @include('websites.partials.page-content')
            </div>
            <aside class="lg:col-span-4">
                <div class="sticky top-28 border border-slate-200 border-t-4 border-t-primary bg-white p-6">
                    <p class="font-mono text-xs tracking-widest text-primary uppercase">Butuh informasi teknis?</p>
                    <p class="mt-3 font-heading text-xl font-bold text-slate-900">Hubungi tim sales engineering kami.</p>
                    <ul class="mt-5 space-y-3 text-sm">
                        @if ($company->phone)<li class="flex gap-3"><x-icon name="phone" class="size-4 text-primary" /> {{ $company->phone }}</li>@endif
                        @if ($company->email)<li class="flex gap-3"><x-icon name="mail" class="size-4 text-primary" /> <span class="break-all">{{ $company->email }}</span></li>@endif
                    </ul>
                    <a href="{{ $site->anchor('contact') }}" class="mt-6 flex items-center justify-center gap-2 rounded-btn bg-primary px-5 py-3 text-xs font-bold tracking-wider text-on-primary uppercase">Request Quote <x-icon name="arrow-right" class="size-4" /></a>
                </div>
            </aside>
        </div>
    </section>
@endsection
