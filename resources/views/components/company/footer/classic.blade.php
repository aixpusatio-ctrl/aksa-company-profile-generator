{{-- Footer: Classic — 4 columns (brand, navigation, services, contact) + bottom bar. --}}
<footer class="tone-inverse bg-surface text-ink">
    <div class="{{ $ds->container() }} grid gap-12 py-16 sm:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <x-site.logo :company="$company" text-class="text-lg font-bold" />
            <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 180) }}</p>
            <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-9 items-center justify-center rounded-full border border-line text-muted transition hover:border-primary hover:text-primary" />
        </div>
        <div class="lg:col-span-2">
            <h3 class="text-sm font-semibold text-ink">Navigasi</h3>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ($menu->filter(fn ($item) => $item->url)->take(7) as $item)
                    <li><a {!! $item->attributes() !!} class="text-muted hover:text-ink">{{ $item->title }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="lg:col-span-3">
            <h3 class="text-sm font-semibold text-ink">Layanan</h3>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ($company->services->take(5) as $service)
                    <li><a href="{{ $site->anchor('services') }}" class="text-muted hover:text-ink">{{ $service->title }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="lg:col-span-3">
            <h3 class="text-sm font-semibold text-ink">Kontak</h3>
            <ul class="mt-5 space-y-3 text-sm text-muted">
                @if ($company->fullAddress())<li>{{ $company->fullAddress() }}</li>@endif
                @if ($company->phone)<li><a href="tel:{{ $company->phone }}" class="hover:text-ink">{{ $company->phone }}</a></li>@endif
                @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="hover:text-ink">{{ $company->email }}</a></li>@endif
            </ul>
        </div>
    </div>
    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col items-center justify-between gap-3 py-6 text-xs text-muted sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            <div class="flex flex-wrap gap-5">
                @foreach ($pages->take(4) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="hover:text-ink">{{ $p->title }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
