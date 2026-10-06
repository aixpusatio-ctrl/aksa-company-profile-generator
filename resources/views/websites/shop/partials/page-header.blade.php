{{--
    Shop page header. Vars: $title, $subtitle (optional), $crumbs ([label => url|null]).
--}}
<section class="relative overflow-hidden border-b border-line bg-surface-alt text-ink">
    <div class="pointer-events-none absolute -top-24 right-0 size-72 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="{{ $ds->container() }} relative py-8 sm:py-12">
        @include('websites.shop.partials.breadcrumb', ['crumbs' => $crumbs ?? []])
        <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0">
                <h1 class="heading text-3xl text-ink sm:text-4xl">{{ $title }}</h1>
                @if (! empty($subtitle))
                    <p class="mt-2 max-w-2xl text-sm text-muted sm:text-base">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
