{{--
    Customer account shell ("Akun Saya"): sidebar navigation separate from the
    seller dashboard. Child views: @extends('websites.shop.account.layout') + @section('account').
    Optional vars: $accountTitle, $accountSubtitle.
--}}
@extends('websites.shop.layout')

@section('shop')
    @include('websites.shop.partials.page-header', [
        'title' => $accountTitle ?? 'Akun Saya',
        'subtitle' => $accountSubtitle ?? ($customer ? 'Halo, '.$customer->name.'!' : null),
        'crumbs' => ['Akun' => $site->account(), ($accountTitle ?? 'Akun Saya') => null],
    ])
    <div class="{{ $ds->container() }} grid gap-6 py-8 sm:py-10 lg:grid-cols-[15rem_1fr] lg:gap-10">
        @include('websites.shop.partials.account-nav')
        <div class="min-w-0">
            @yield('account')
        </div>
    </div>
@endsection
