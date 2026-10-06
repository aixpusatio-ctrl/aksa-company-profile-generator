{{-- Footer: CTA — a full-width primary call-to-action panel overlapping the section above, followed by link columns. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
@endphp
<footer class="relative bg-surface-alt text-ink">
    <div class="{{ $ds->container() }} relative z-10 -mt-12 lg:-mt-16">
        <div class="tone-primary relative overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-primary px-6 py-10 text-on-primary shadow-[0_30px_60px_-30px_rgb(0_0_0/0.45)] sm:px-12 sm:py-14 lg:px-16" {!! $ds->reveal() !!}>
            <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 size-80 rounded-full bg-on-primary/10 blur-2xl"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-32 left-1/3 size-72 rounded-full bg-secondary/30 blur-3xl"></div>
            <div class="relative grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] uppercase opacity-80">Mari bekerja sama</p>
                    <p class="heading mt-3 max-w-2xl text-[clamp(1.9rem,4vw,3.25rem)]">Siap memulai proyek berikutnya bersama {{ $company->name }}?</p>
                    @if ($company->tagline)<p class="mt-4 max-w-xl text-base opacity-80">{{ $company->tagline }}</p>@endif
                </div>
                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                    <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('light', '!px-7 !py-4') }}">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
                    @if ($company->whatsappUrl())
                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-btn border border-on-primary/40 px-7 py-3.5 text-sm font-semibold transition hover:bg-on-primary/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="{{ $ds->container() }} grid grid-cols-2 gap-x-6 gap-y-12 py-16 lg:grid-cols-12">
        <div class="col-span-2 lg:col-span-4">
            <a href="{{ $site->home() }}" class="inline-block text-ink"><x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" /></a>
            @if ($company->description)
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 170) }}</p>
            @endif
            <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-10 items-center justify-center rounded-full bg-card text-muted ring-1 ring-line transition hover:text-primary hover:ring-primary" />
        </div>
        <div class="lg:col-span-2 lg:col-start-6">
            <h3 class="text-sm font-semibold text-ink">Navigasi</h3>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ($links as $item)
                    <li><a {!! $item->attributes() !!} class="text-muted transition hover:text-ink">{{ $item->title }}</a></li>
                @endforeach
            </ul>
        </div>
        @if ($company->services->isNotEmpty())
            <div class="lg:col-span-3">
                <h3 class="text-sm font-semibold text-ink">Layanan</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($company->services->take(6) as $service)
                        <li><a href="{{ $site->anchor('services') }}" class="text-muted transition hover:text-ink">{{ $service->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="lg:col-span-2">
            <h3 class="text-sm font-semibold text-ink">Kontak</h3>
            <ul class="mt-5 space-y-3 text-sm text-muted">
                @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-ink">{{ $company->phone }}</a></li>@endif
                @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="break-all transition hover:text-ink">{{ $company->email }}</a></li>@endif
                @if ($company->city)<li>{{ collect([$company->city, $company->province])->filter()->implode(', ') }}</li>@endif
            </ul>
        </div>
    </div>

    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col gap-3 py-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            <div class="flex flex-wrap gap-5">
                @foreach ($pages->take(4) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
