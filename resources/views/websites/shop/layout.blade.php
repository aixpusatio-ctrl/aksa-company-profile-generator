{{--
    Shop shell: renders inside the company's own template layout ($shopLayout)
    so the navbar, footer and branding stay identical to the rest of the site.
    Hand-crafted layouts get a design-system wrapper ($ds->bodyClass()) so the
    shop's theme tokens (bg-surface, text-ink, border-line ...) resolve.

    Child views: @extends('websites.shop.layout') + @section('shop').
--}}
@extends($shopLayout)

@php
    // Layouts whose header floats (fixed + transparent) over the first section.
    $shopSpacer = match ($layout) {
        'executive' => 'h-20 lg:h-24 bg-secondary',
        'modern-business' => 'h-20 bg-linear-to-r from-primary to-secondary',
        default => null,
    };
@endphp

@section('content')
    <div x-data class="shop-root {{ $isCrafted ? $ds->bodyClass() : 'bg-surface text-ink' }}" data-shop-root>
        @if ($shopSpacer)
            <div class="{{ $shopSpacer }}" aria-hidden="true"></div>
        @endif

        @include('websites.shop.partials.shop-bar')

        @yield('shop')

    </div>
@endsection
