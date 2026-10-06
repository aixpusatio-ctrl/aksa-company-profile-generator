@extends('websites.templates.minimal.layout')

@section('content')
    <section class="mx-auto max-w-4xl px-6 pt-16 pb-12 md:pt-28 md:pb-16">
        <p class="text-sm text-neutral-400"><a href="{{ $site->home() }}" class="hover:text-neutral-950">{{ $company->name }}</a> / {{ $page->title }}</p>
        <h1 class="mt-6 font-heading text-5xl leading-[1.02] font-semibold tracking-tighter text-neutral-950 md:text-7xl">{{ $page->title }}</h1>
    </section>
    <section class="mx-auto max-w-4xl px-6">
        <div class="grid gap-8 border-t border-neutral-200 py-12 md:grid-cols-4 md:py-16">
            <div class="text-xs text-neutral-400">
                @if ($page->updated_at)
                    <p>Diperbarui</p>
                    <p class="mt-1 text-neutral-900">{{ $page->updated_at->translatedFormat('d F Y') }}</p>
                @endif
            </div>
            <div class="text-[17px] text-neutral-700 md:col-span-3">
                @include('websites.partials.page-content')
            </div>
        </div>
    </section>
@endsection
