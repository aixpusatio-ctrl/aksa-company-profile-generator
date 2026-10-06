{{-- Footer: Centered — airy, elegant: centered logo, tagline, horizontal navigation, socials and copyright. --}}
@php
    $links = $menu->filter(fn ($item) => $item->url)->take(7);
@endphp
<footer class="border-t border-line bg-surface text-ink">
    <div class="{{ $ds->container('narrow') }} flex flex-col items-center py-20 text-center sm:py-24">
        <a href="{{ $site->home() }}" class="text-ink" {!! $ds->reveal() !!}><x-site.logo :company="$company" img-class="h-14 w-auto" text-class="text-2xl font-semibold tracking-tight" /></a>
        @if ($company->tagline)
            <p class="heading mt-6 max-w-xl text-xl text-muted sm:text-2xl" {!! $ds->reveal(1) !!}>{{ $company->tagline }}</p>
        @endif
        <nav class="mt-10 flex flex-wrap justify-center gap-x-8 gap-y-3 text-sm font-medium" aria-label="Footer" {!! $ds->reveal(2) !!}>
            @foreach ($links as $item)
                <a {!! $item->attributes() !!} class="text-ink/75 transition hover:text-primary">{{ $item->title }}</a>
            @endforeach
        </nav>
        <x-site.social :company="$company" class="mt-10 justify-center gap-3" link-class="inline-flex size-11 items-center justify-center rounded-full border border-line text-muted transition hover:border-primary hover:text-primary" />
        @if ($company->email || $company->phone)
            <p class="mt-8 flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm text-muted">
                @if ($company->email)<a href="mailto:{{ $company->email }}" class="transition hover:text-ink">{{ $company->email }}</a>@endif
                @if ($company->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-ink">{{ $company->phone }}</a>@endif
            </p>
        @endif
        <div class="mt-12 h-px w-16 bg-line"></div>
        <div class="mt-8 flex flex-col items-center gap-3 text-xs text-muted">
            @if ($pages->isNotEmpty())
                <div class="flex flex-wrap justify-center gap-x-5 gap-y-1">
                    @foreach ($pages->take(4) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                    @endforeach
                </div>
            @endif
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</footer>
