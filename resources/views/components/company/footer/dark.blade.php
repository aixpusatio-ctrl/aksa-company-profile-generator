{{-- Footer: Dark — dark multi-column footer with a contact strip, social icons and a subtle brand glow. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
    $contacts = collect([
        ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null],
        ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
        ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
    ])->filter(fn ($c) => filled($c[2]));
@endphp
<footer class="{{ $ds->isDark() ? '' : 'tone-inverse' }} relative overflow-hidden bg-surface text-ink">
    <div aria-hidden="true" class="pointer-events-none absolute -top-40 left-1/2 h-80 w-[min(60rem,120%)] -translate-x-1/2 rounded-full bg-primary/20 blur-[120px]"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-primary/60 to-transparent"></div>

    <div class="{{ $ds->container() }} relative">
        @if ($contacts->isNotEmpty())
            <div class="grid divide-y divide-line border-b border-line md:grid-cols-3 md:divide-x md:divide-y-0">
                @foreach ($contacts as [$icon, $label, $value, $href])
                    <div class="flex items-center gap-4 py-7 md:px-8 md:first:pl-0">
                        <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary ring-1 ring-primary/25"><x-icon :name="$icon" class="size-5" /></span>
                        <span class="min-w-0">
                            <span class="block text-xs tracking-[0.16em] text-muted uppercase">{{ $label }}</span>
                            @if ($href)
                                <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-1 line-clamp-2 block font-semibold break-words text-ink transition hover:text-primary">{{ $value }}</a>
                            @else
                                <span class="mt-1 line-clamp-2 block font-semibold text-ink">{{ $value }}</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-2 gap-x-6 gap-y-12 py-16 lg:grid-cols-12">
            <div class="col-span-2 lg:col-span-5 lg:pr-12">
                <a href="{{ $site->home() }}" class="inline-block text-ink"><x-site.logo :company="$company" text-class="text-xl font-bold tracking-tight" /></a>
                @if ($company->description)
                    <p class="mt-6 max-w-md text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 220) }}</p>
                @endif
                <x-site.social :company="$company" class="mt-8" link-class="inline-flex size-11 items-center justify-center rounded-full bg-ink/5 text-ink ring-1 ring-line transition hover:bg-primary hover:text-on-primary hover:ring-primary" />
            </div>
            <div class="lg:col-span-2">
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
                <h3 class="text-sm font-semibold text-ink">Informasi</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($pages->take(5) as $p)
                        <li><a href="{{ $site->page($p->slug) }}" class="text-muted transition hover:text-ink">{{ $p->title }}</a></li>
                    @endforeach
                    @if ($company->working_hours)
                        <li class="pt-2 text-muted"><span class="block text-xs tracking-[0.16em] uppercase">Jam Kerja</span><span class="mt-1 block text-ink">{{ $company->working_hours }}</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="relative border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col gap-3 py-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            @if ($company->established_year)<p>Melayani sejak {{ $company->established_year }}</p>@endif
        </div>
    </div>
</footer>
