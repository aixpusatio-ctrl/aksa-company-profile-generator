{{-- Footer: Institutional — formal footer: identity row with full address and official contacts, four link columns, legal bottom row. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
    $heading = 'border-b border-line pb-3 text-sm font-bold text-ink';
@endphp
<footer class="border-t-4 border-primary bg-surface-alt text-ink">
    <div class="{{ $ds->container('wide') }}">
        <div class="grid grid-cols-1 gap-8 border-b border-line py-12 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] lg:items-center">
            <div class="flex min-w-0 items-start gap-5">
                <x-site.logo :company="$company" img-class="h-16 w-auto" text-class="sr-only" />
                <div class="min-w-0">
                    <p class="heading text-xl break-words sm:text-2xl">{{ $company->name }}</p>
                    @if ($company->tagline)<p class="mt-1 text-sm text-muted">{{ $company->tagline }}</p>@endif
                    @if ($company->fullAddress())
                        <p class="mt-4 flex gap-2 text-sm leading-relaxed text-muted"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-primary" />{{ $company->fullAddress() }}</p>
                    @endif
                </div>
            </div>
            <dl class="grid min-w-0 grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                @foreach ([['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null], ['mail', 'Email resmi', $company->email, $company->email ? 'mailto:'.$company->email : null], ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()], ['clock', 'Jam layanan', $company->working_hours, null]] as [$icon, $label, $value, $href])
                    @if ($value)
                        <div class="flex min-w-0 items-center gap-3 rounded-brand border border-line bg-card px-4 py-3">
                            <x-icon :name="$icon" class="size-5 shrink-0 text-primary" />
                            <div class="min-w-0">
                                <dt class="text-[11px] font-semibold tracking-[0.12em] text-muted uppercase">{{ $label }}</dt>
                                <dd class="truncate font-medium text-ink">
                                    @if ($href)<a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="hover:text-primary">{{ $value }}</a>@else{{ $value }}@endif
                                </dd>
                            </div>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>

        <div class="grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <h3 class="{{ $heading }}">Tautan Utama</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($links as $item)
                        <li><a {!! $item->attributes() !!} class="inline-flex items-center gap-2 text-muted transition hover:text-primary"><x-icon name="chevron-right" class="size-3 text-primary" />{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="{{ $heading }}">Layanan</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @forelse ($company->services->take(6) as $service)
                        <li><a href="{{ $site->anchor('services') }}" class="inline-flex items-center gap-2 text-muted transition hover:text-primary"><x-icon name="chevron-right" class="size-3 shrink-0 text-primary" />{{ $service->title }}</a></li>
                    @empty
                        <li><a href="{{ $site->anchor('about') }}" class="text-muted hover:text-primary">Tentang Kami</a></li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3 class="{{ $heading }}">Informasi</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($pages->take(6) as $p)
                        <li><a href="{{ $site->page($p->slug) }}" class="inline-flex items-center gap-2 text-muted transition hover:text-primary"><x-icon name="chevron-right" class="size-3 text-primary" />{{ $p->title }}</a></li>
                    @endforeach
                    <li><a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 text-muted transition hover:text-primary"><x-icon name="chevron-right" class="size-3 text-primary" />Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h3 class="{{ $heading }}">Ikuti Kami</h3>
                @if ($company->description)
                    <p class="mt-4 text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 120) }}</p>
                @endif
                <x-site.social :company="$company" class="mt-5" link-class="inline-flex size-10 items-center justify-center rounded-brand bg-primary text-on-primary transition hover:brightness-110" />
            </div>
        </div>
    </div>

    <div class="{{ $ds->isDark() ? '' : 'tone-inverse' }} bg-surface text-ink">
        <div class="{{ $ds->container('wide') }} flex flex-col gap-3 py-5 text-xs text-muted md:flex-row md:items-center md:justify-between">
            <p>Hak Cipta &copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak dilindungi undang-undang.</p>
            <div class="flex flex-wrap gap-x-5 gap-y-1">
                @foreach ($pages->take(4) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                @endforeach
                <a href="#main" class="transition hover:text-ink">Kembali ke atas ↑</a>
            </div>
        </div>
    </div>
</footer>
