{{-- Shop hero: Banner — wide image banner with text panel. --}}
<section class="py-5 sm:py-8">
    <div class="{{ $ds->container() }}">
        <div class="relative isolate overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-secondary text-white">
            @if ($banner)
                <img src="{{ $banner }}" alt="" class="absolute inset-0 -z-10 size-full object-cover">
            @endif
            <div class="absolute inset-0 -z-10 bg-linear-to-r from-black/75 via-black/45 to-black/5"></div>
            <div class="max-w-2xl px-6 py-14 sm:px-12 sm:py-20 lg:py-24">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur"><x-shop.icon name="bag" class="size-3.5" /> {{ $shop->displayName() }}</span>
                <h1 class="heading mt-4 text-4xl text-white sm:text-5xl lg:text-6xl">{{ $shopTitle }}</h1>
                <p class="mt-4 max-w-xl text-base text-white/80 sm:text-lg">{{ $shopSubtitle }}</p>
                @include('websites.shop.partials.hero._actions', ['heroLight' => true])
            </div>
        </div>
        @include('websites.shop.partials.hero._perks')
    </div>
</section>
