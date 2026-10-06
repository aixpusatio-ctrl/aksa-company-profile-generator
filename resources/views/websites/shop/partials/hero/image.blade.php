{{-- Shop hero: Image — full-bleed photo with centered copy. --}}
<section class="relative isolate flex min-h-[26rem] items-center overflow-hidden bg-secondary text-white sm:min-h-[34rem]">
    @if ($banner)
        <img src="{{ $banner }}" alt="" class="absolute inset-0 -z-10 size-full object-cover animate-ken-burns">
    @endif
    <div class="absolute inset-0 -z-10 bg-black/50"></div>
    <div class="{{ $ds->container() }} py-16 text-center">
        <p class="text-xs font-semibold tracking-[0.3em] text-white/75 uppercase">{{ $shop->displayName() }}</p>
        <h1 class="heading mx-auto mt-4 max-w-4xl text-display text-white">{{ $shopTitle }}</h1>
        <p class="mx-auto mt-5 max-w-2xl text-base text-white/80 sm:text-lg">{{ $shopSubtitle }}</p>
        <div class="flex justify-center">@include('websites.shop.partials.hero._actions', ['heroLight' => true])</div>
    </div>
</section>
<div class="{{ $ds->container() }}">@include('websites.shop.partials.hero._perks')</div>
