{{-- Footer: Wordmark — link columns on top, finished by a gigantic edge-to-edge company wordmark. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
    $wordmark = \Illuminate\Support\Str::of($company->name)->replaceMatches('/^(PT|CV)\.?\s+/i', '')->trim()->toString();
    $wordmarkVw = round(min(20, ($ds->get('heading') === 'display-upper' ? 118 : 148) / max(mb_strlen($wordmark), 4)), 2);
@endphp
<footer class="relative overflow-hidden border-t border-line bg-surface text-ink">
    <div class="{{ $ds->container('wide') }} grid grid-cols-2 gap-x-6 gap-y-12 pt-16 pb-10 sm:pt-20 lg:grid-cols-12">
        <div class="col-span-2 lg:col-span-4">
            <p class="heading max-w-sm text-2xl">{{ $company->tagline ?: \Illuminate\Support\Str::limit($company->description, 90) }}</p>
            <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'mt-7') }}">Mulai Proyek <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="lg:col-span-2 lg:col-start-6">
            <h3 class="font-mono text-xs tracking-[0.14em] text-muted uppercase">Menu</h3>
            <ul class="mt-5 space-y-2.5 text-sm">
                @foreach ($links as $item)
                    <li><a {!! $item->attributes() !!} class="text-ink/80 transition hover:text-primary">{{ $item->title }}</a></li>
                @endforeach
            </ul>
        </div>
        @if ($company->services->isNotEmpty())
            <div class="lg:col-span-3">
                <h3 class="font-mono text-xs tracking-[0.14em] text-muted uppercase">Layanan</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach ($company->services->take(6) as $service)
                        <li><a href="{{ $site->anchor('services') }}" class="text-ink/80 transition hover:text-primary">{{ $service->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="lg:col-span-2">
            <h3 class="font-mono text-xs tracking-[0.14em] text-muted uppercase">Kontak</h3>
            <ul class="mt-5 space-y-2.5 text-sm text-ink/80">
                @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="break-all transition hover:text-primary">{{ $company->email }}</a></li>@endif
                @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-primary">{{ $company->phone }}</a></li>@endif
                @if ($company->city)<li class="text-muted">{{ collect([$company->city, $company->country])->filter()->implode(', ') }}</li>@endif
            </ul>
            <x-site.social :company="$company" class="mt-5 -ml-2 gap-0.5" link-class="inline-flex size-10 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink" />
        </div>
    </div>
    <div class="{{ $ds->container('wide') }} flex flex-col gap-3 border-t border-line pt-6 text-xs text-muted sm:flex-row sm:justify-between">
        <p>&copy; {{ date('Y') }} {{ $company->name }}</p>
        <div class="flex flex-wrap gap-5">
            @foreach ($pages->take(4) as $p)
                <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
            @endforeach
        </div>
    </div>
    <div aria-hidden="true" class="mt-6 overflow-hidden select-none">
        <p class="heading translate-y-[14%] text-center leading-[0.8] whitespace-nowrap text-ink" style="font-size: {{ $wordmarkVw }}vw" {!! $ds->reveal() !!}>{{ $wordmark }}</p>
    </div>
</footer>
