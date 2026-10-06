{{-- Footer: Minimal — one quiet row: logo, a few links, socials and copyright above a thin hairline. --}}
@php
    $links = $menu->filter(fn ($item) => $item->url)->take(5);
@endphp
<footer class="border-t border-line bg-surface text-ink">
    <div class="{{ $ds->container('wide') }} flex flex-col items-center gap-6 py-10 text-center lg:flex-row lg:justify-between lg:text-left">
        <a href="{{ $site->home() }}" class="text-ink"><x-site.logo :company="$company" text-class="text-base font-semibold tracking-tight" /></a>
        <nav class="flex flex-wrap justify-center gap-x-7 gap-y-2 text-sm text-muted" aria-label="Footer">
            @foreach ($links as $item)
                <a {!! $item->attributes() !!} class="transition hover:text-ink">{{ $item->title }}</a>
            @endforeach
            @foreach ($pages->take(2) as $p)
                <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
            @endforeach
        </nav>
        <div class="flex flex-col items-center gap-4 sm:flex-row sm:gap-6">
            <x-site.social :company="$company" class="gap-1" link-class="inline-flex size-10 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink" />
            <p class="text-xs text-muted">&copy; {{ date('Y') }} {{ $company->name }}</p>
        </div>
    </div>
</footer>
