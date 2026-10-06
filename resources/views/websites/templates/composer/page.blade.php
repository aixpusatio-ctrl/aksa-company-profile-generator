@extends('websites.templates.composer.layout')

@section('content')
    @include($ds->view('page-header'))

    <section class="{{ $ds->section('base') }}">
        <div class="{{ $ds->container('text') }}">
            <div {!! $ds->reveal() !!}>
                @include('websites.partials.page-content')
            </div>
        </div>
    </section>

    @if ($pages->count() > 1)
        <section class="border-t border-line bg-surface-alt py-12">
            <div class="{{ $ds->container() }} flex flex-wrap items-center gap-3">
                <span class="mr-2 text-sm font-semibold text-muted">Halaman lainnya</span>
                @foreach ($pages->where('slug', '!=', $page->slug) as $other)
                    <a href="{{ $site->page($other->slug) }}" class="rounded-full border border-line px-4 py-2 text-sm text-ink transition hover:border-primary hover:text-primary">{{ $other->title }}</a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
