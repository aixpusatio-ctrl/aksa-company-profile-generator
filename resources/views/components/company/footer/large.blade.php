{{-- Footer: Large — oversized "Mari bekerja sama" CTA row, five columns (brand, navigation, services, pages, contact) and a bottom bar. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
@endphp
<footer class="border-t border-line bg-surface text-ink">
    <div class="{{ $ds->container() }} grid gap-10 py-16 sm:py-24 lg:grid-cols-[1fr_auto] lg:items-end">
        <div {!! $ds->reveal() !!}>
            {!! $ds->eyebrow('Mari bekerja sama') !!}
            <p class="heading mt-5 max-w-4xl text-[clamp(2.4rem,6.5vw,5.75rem)]">Punya rencana besar? <span class="text-primary">Mari wujudkan bersama.</span></p>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center lg:flex-col lg:items-end" {!! $ds->reveal(1) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Diskusi', 'kind' => 'primary', 'class' => 'w-full sm:w-auto !px-8 !py-4'])
            @if ($company->email)
                <a href="mailto:{{ $company->email }}" class="text-sm font-medium break-all text-muted underline decoration-line underline-offset-4 transition hover:text-ink">{{ $company->email }}</a>
            @endif
        </div>
    </div>

    <div class="{{ $ds->container() }}">
        <div class="grid grid-cols-2 gap-x-6 gap-y-12 border-t border-line py-14 lg:grid-cols-12 lg:gap-8">
            <div class="col-span-2 lg:col-span-4 lg:pr-10">
                <a href="{{ $site->home() }}" class="inline-block text-ink"><x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" /></a>
                @if ($company->description)
                    <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 200) }}</p>
                @endif
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-10 items-center justify-center rounded-full border border-line text-muted transition hover:border-primary hover:bg-primary hover:text-on-primary" />
            </div>
            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Navigasi</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($links as $item)
                        <li><a {!! $item->attributes() !!} class="text-ink/80 transition hover:text-primary">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @if ($company->services->isNotEmpty())
                <div class="lg:col-span-2">
                    <h3 class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Layanan</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($company->services->take(6) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="text-ink/80 transition hover:text-primary">{{ $service->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($pages->isNotEmpty())
                <div class="lg:col-span-2">
                    <h3 class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Halaman</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($pages->take(6) as $p)
                            <li><a href="{{ $site->page($p->slug) }}" class="text-ink/80 transition hover:text-primary">{{ $p->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Kontak</h3>
                <ul class="mt-5 space-y-3 text-sm text-ink/80">
                    @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-primary">{{ $company->phone }}</a></li>@endif
                    @if ($company->whatsappUrl())<li><a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-primary">WhatsApp</a></li>@endif
                    @if ($company->fullAddress())<li class="leading-relaxed text-muted">{{ $company->fullAddress() }}</li>@endif
                    @if ($company->working_hours)<li class="text-muted">{{ $company->working_hours }}</li>@endif
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col gap-4 py-7 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            <a href="#main" class="inline-flex items-center gap-2 font-medium text-ink transition hover:text-primary">Kembali ke atas <x-icon name="arrow-right" class="size-3.5 -rotate-90" /></a>
        </div>
    </div>
</footer>
