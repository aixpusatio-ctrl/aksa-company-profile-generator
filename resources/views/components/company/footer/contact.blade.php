{{-- Footer: Contact — three large contact cards (visit, call, write) followed by a slim links row. --}}
@php
    $phoneHref = $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null;
    $cards = collect([
        $company->fullAddress() ? ['map-pin', 'Kunjungi kami', $company->fullAddress(), $company->google_maps_url, $company->google_maps_url ? 'Buka peta' : null] : null,
        ($company->phone || $company->whatsapp) ? ['phone', 'Telepon & WhatsApp', $company->phone ?: $company->whatsapp, $company->whatsappUrl() ?: $phoneHref, $company->whatsappUrl() ? 'Chat WhatsApp' : 'Telepon sekarang'] : null,
        ($company->email || $company->working_hours) ? ['mail', 'Email & jam kerja', $company->email ?: $company->working_hours, $company->email ? 'mailto:'.$company->email : null, $company->email ? 'Kirim email' : null, $company->email ? $company->working_hours : null] : null,
    ])->filter()->values();
@endphp
<footer class="border-t border-line bg-surface-alt text-ink">
    <div class="{{ $ds->container() }} py-16 sm:py-20">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <p class="heading max-w-xl text-[clamp(1.75rem,3.2vw,2.75rem)]" {!! $ds->reveal() !!}>Kami siap mendengar kebutuhan Anda.</p>
            <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary') }}" {!! $ds->reveal(1) !!}>Kirim Pesan <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        @if ($cards->isNotEmpty())
            <div class="mt-10 grid gap-4 {{ [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3'][$cards->count()] }}">
                @foreach ($cards as $card)
                    @php([$icon, $label, $value, $href, $action] = $card)
                    <div class="{{ $ds->card('flex flex-col p-7 sm:p-8') }}" {!! $ds->reveal($loop->index + 1) !!}>
                        <span class="inline-flex size-12 items-center justify-center rounded-brand bg-primary text-on-primary"><x-icon :name="$icon" class="size-5" /></span>
                        <p class="mt-6 text-xs font-semibold tracking-[0.16em] text-muted uppercase">{{ $label }}</p>
                        <p class="mt-2 text-lg leading-snug font-semibold break-words text-ink">{{ $value }}</p>
                        @if (! empty($card[5]))<p class="mt-2 text-sm text-muted">{{ $card[5] }}</p>@endif
                        @if ($href && $action)
                            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-primary transition hover:gap-3">{{ $action }} <x-icon name="arrow-up-right" class="size-4" /></a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col items-center gap-5 py-7 text-sm lg:flex-row lg:justify-between">
            <a href="{{ $site->home() }}" class="text-ink"><x-site.logo :company="$company" text-class="text-base font-bold tracking-tight" /></a>
            <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-muted" aria-label="Footer">
                @foreach ($menu->filter(fn ($item) => $item->url)->take(6) as $item)
                    <a {!! $item->attributes() !!} class="transition hover:text-ink">{{ $item->title }}</a>
                @endforeach
                @foreach ($pages->take(2) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                @endforeach
            </nav>
            <x-site.social :company="$company" class="gap-1" link-class="inline-flex size-10 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink" />
        </div>
        <p class="pb-7 text-center text-xs text-muted">&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
    </div>
</footer>
