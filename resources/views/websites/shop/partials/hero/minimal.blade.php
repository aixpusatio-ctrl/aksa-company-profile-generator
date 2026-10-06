{{-- Shop hero: Minimal — big typographic title, category chips. --}}
<section class="border-b border-line py-14 sm:py-20">
    <div class="{{ $ds->container() }} text-center">
        {!! $ds->eyebrow($shop->displayName()) !!}
        <h1 class="heading mx-auto mt-4 max-w-4xl text-display text-ink">{{ $shopTitle }}</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lead text-muted">{{ $shopSubtitle }}</p>
        <div class="flex justify-center">@include('websites.shop.partials.hero._actions')</div>
        @if ($categoriesTree->isNotEmpty())
            <div class="mx-auto mt-10 flex max-w-3xl flex-wrap justify-center gap-2">
                @foreach ($categoriesTree as $cat)
                    <a href="{{ $site->shop('category/'.$cat->slug) }}" class="rounded-full border border-line px-4 py-1.5 text-sm text-muted transition hover:border-ink hover:text-ink">{{ $cat->name }}</a>
                @endforeach
            </div>
        @endif
    </div>
</section>
