{{-- Footer: Multicolumn — light, app-like directory footer: brand row on top, then six neat columns (navigation, services, products, pages, contact, hours). --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
    $heading = 'text-xs font-semibold tracking-[0.14em] text-ink uppercase';
    $linkClass = 'text-muted transition hover:text-primary';
@endphp
<footer class="border-t border-line bg-surface-alt text-ink">
    <div class="{{ $ds->container() }} py-14 sm:py-16">
        <div class="flex flex-col gap-6 border-b border-line pb-10 md:flex-row md:items-center md:justify-between">
            <div class="flex max-w-xl flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                <a href="{{ $site->home() }}" class="shrink-0 text-ink"><x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" /></a>
                @if ($company->tagline)
                    <p class="text-sm text-muted sm:border-l sm:border-line sm:pl-6">{{ $company->tagline }}</p>
                @endif
            </div>
            <x-site.social :company="$company" link-class="inline-flex size-10 items-center justify-center rounded-brand border border-line bg-card text-muted transition hover:border-primary hover:text-primary" />
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-10 pt-10 text-sm sm:grid-cols-3 lg:grid-cols-6">
            <div>
                <h3 class="{{ $heading }}">Navigasi</h3>
                <ul class="mt-4 space-y-2.5">
                    @foreach ($links as $item)
                        <li><a {!! $item->attributes() !!} class="{{ $linkClass }}">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @if ($company->services->isNotEmpty())
                <div>
                    <h3 class="{{ $heading }}">Layanan</h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($company->services->take(6) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="{{ $linkClass }}">{{ $service->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($company->products->isNotEmpty())
                <div>
                    <h3 class="{{ $heading }}">Produk</h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($company->products->take(6) as $product)
                            <li><a href="{{ $site->anchor('products') }}" class="{{ $linkClass }}">{{ $product->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($pages->isNotEmpty())
                <div>
                    <h3 class="{{ $heading }}">Halaman</h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($pages->take(6) as $p)
                            <li><a href="{{ $site->page($p->slug) }}" class="{{ $linkClass }}">{{ $p->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="col-span-2 sm:col-span-1">
                <h3 class="{{ $heading }}">Kontak</h3>
                <ul class="mt-4 space-y-2.5 text-muted">
                    @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="{{ $linkClass }}">{{ $company->phone }}</a></li>@endif
                    @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="{{ $linkClass }} break-all">{{ $company->email }}</a></li>@endif
                    @if ($company->fullAddress())<li class="leading-relaxed">{{ $company->fullAddress() }}</li>@endif
                </ul>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <h3 class="{{ $heading }}">Jam Operasional</h3>
                <div class="mt-4 space-y-3 text-muted">
                    @if ($company->working_hours)
                        <p class="flex gap-2"><x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-primary" /><span>{{ $company->working_hours }}</span></p>
                    @endif
                    @if ($company->whatsappUrl())
                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-full border border-line bg-card px-3.5 py-2 text-xs font-semibold text-ink transition hover:border-primary hover:text-primary"><x-icon name="whatsapp" class="size-4" /> Chat WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col gap-2 py-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            @if ($company->city)<p>{{ collect([$company->city, $company->country])->filter()->implode(', ') }}</p>@endif
        </div>
    </div>
</footer>
