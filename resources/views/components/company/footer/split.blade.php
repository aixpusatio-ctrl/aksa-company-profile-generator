{{-- Footer: Split — two halves: a brand-colored panel with tagline and CTA on the left, link columns and contact on the right. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
@endphp
<footer class="border-t border-line bg-surface text-ink">
    <div class="grid lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
        <div class="tone-primary relative overflow-hidden bg-primary px-5 py-16 text-on-primary sm:px-10 lg:px-14 lg:py-20">
            <div aria-hidden="true" class="pointer-events-none absolute -right-20 -bottom-20 size-72 rounded-full border border-on-primary/15"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-8 -bottom-8 size-44 rounded-full border border-on-primary/15"></div>
            <div class="relative max-w-md lg:ml-auto lg:mr-0">
                <a href="{{ $site->home() }}" class="inline-block"><x-site.logo :company="$company" box-class="bg-on-primary text-primary" text-class="text-lg font-bold tracking-tight" /></a>
                <p class="heading mt-10 text-[clamp(1.75rem,3vw,2.5rem)]">{{ $company->tagline ?: 'Mari bangun sesuatu yang berarti bersama.' }}</p>
                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('light', 'mt-8') }}">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
                <x-site.social :company="$company" class="mt-10" link-class="inline-flex size-10 items-center justify-center rounded-full border border-on-primary/25 transition hover:bg-on-primary hover:text-primary" />
            </div>
        </div>
        <div class="px-5 py-16 sm:px-10 lg:px-14 lg:py-20">
            <div class="grid max-w-3xl gap-10 sm:grid-cols-3">
                <div>
                    <h3 class="text-xs font-semibold tracking-[0.16em] text-muted uppercase">Navigasi</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($links as $item)
                            <li><a {!! $item->attributes() !!} class="text-ink/80 transition hover:text-primary">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @if ($company->services->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-semibold tracking-[0.16em] text-muted uppercase">Layanan</h3>
                        <ul class="mt-5 space-y-3 text-sm">
                            @foreach ($company->services->take(6) as $service)
                                <li><a href="{{ $site->anchor('services') }}" class="text-ink/80 transition hover:text-primary">{{ $service->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <h3 class="text-xs font-semibold tracking-[0.16em] text-muted uppercase">Kontak</h3>
                    <ul class="mt-5 space-y-3 text-sm text-ink/80">
                        @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-primary">{{ $company->phone }}</a></li>@endif
                        @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="break-all transition hover:text-primary">{{ $company->email }}</a></li>@endif
                        @if ($company->fullAddress())<li class="leading-relaxed text-muted">{{ $company->fullAddress() }}</li>@endif
                        @if ($company->working_hours)<li class="text-muted">{{ $company->working_hours }}</li>@endif
                    </ul>
                </div>
            </div>
            <div class="mt-14 flex max-w-3xl flex-col gap-3 border-t border-line pt-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
                <div class="flex flex-wrap gap-5">
                    @foreach ($pages->take(3) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
