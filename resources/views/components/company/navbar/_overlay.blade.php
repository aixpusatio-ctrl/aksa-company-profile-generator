{{--
    Shared full-screen menu overlay for navbar variants (lives inside x-data="siteNav").
    Params: $hideClass (breakpoint where it disappears, e.g. 'lg:hidden'; '' = always available),
            $large (bool: giant heading-style links + contact column on wide screens).
--}}
@php
    $hideClass ??= 'lg:hidden';
    $large ??= false;
@endphp
<div x-cloak x-show="open" x-transition.opacity.duration.300ms
     x-effect="document.documentElement.style.overflow = open ? 'hidden' : ''"
     @keydown.escape.window="close()"
     class="fixed inset-0 z-50 flex flex-col overflow-y-auto bg-surface text-ink {{ $hideClass }}"
     role="dialog" aria-modal="true" aria-label="Menu navigasi">
    <div class="{{ $ds->container() }} flex h-16 shrink-0 items-center justify-between gap-4 sm:h-20">
        <a href="{{ $site->home() }}" @click="close()" class="min-w-0 text-ink">
            <x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" />
        </a>
        <button type="button" @click="close()" class="inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-line text-ink transition hover:border-ink" aria-label="Tutup menu">
            <x-icon name="x" class="size-5" />
        </button>
    </div>

    <div class="{{ $ds->container() }} grid flex-1 content-between gap-12 pt-6 pb-10 sm:pt-10 {{ $large ? 'lg:grid-cols-[1fr_22rem] lg:content-center lg:gap-20' : '' }}">
        <nav aria-label="Menu utama" class="{{ $large ? '' : 'divide-y divide-line border-y border-line' }}">
            @foreach ($menu as $item)
                <div x-data="{ sub: false }" class="{{ $large ? '' : 'py-1' }}">
                    @if ($item->hasChildren())
                        <button type="button" @click="sub = !sub" :aria-expanded="sub"
                                class="group flex w-full items-baseline gap-4 py-2 text-left {{ $item->active ? 'text-primary' : 'text-ink' }}">
                            <span class="w-7 shrink-0 font-mono text-xs text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="heading min-w-0 flex-1 {{ $large ? 'text-[clamp(2rem,7vw,4.5rem)]' : 'text-2xl sm:text-3xl' }} transition group-hover:text-primary">{{ $item->title }}</span>
                            <x-icon name="chevron-down" class="size-5 shrink-0 self-center text-muted transition" ::class="sub && 'rotate-180'" />
                        </button>
                        <div x-show="sub" x-collapse>
                            <div class="flex flex-col gap-1 pt-1 pb-4 pl-11">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="py-1.5 text-base text-muted transition hover:text-ink">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()"
                           class="group flex items-baseline gap-4 py-2 {{ $item->active ? 'text-primary' : 'text-ink' }}">
                            <span class="w-7 shrink-0 font-mono text-xs text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="heading min-w-0 flex-1 {{ $large ? 'text-[clamp(2rem,7vw,4.5rem)]' : 'text-2xl sm:text-3xl' }} transition group-hover:text-primary">{{ $item->title }}</span>
                            <x-icon name="arrow-up-right" class="size-5 shrink-0 self-center text-muted opacity-0 transition group-hover:opacity-100" />
                        </a>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="space-y-8 {{ $large ? 'lg:border-l lg:border-line lg:pl-12' : '' }}">
            <div class="grid gap-5 text-sm sm:grid-cols-2 {{ $large ? 'lg:grid-cols-1' : '' }}">
                @if ($company->phone || $company->email)
                    <div>
                        <p class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Kontak</p>
                        @if ($company->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="mt-2 block font-medium text-ink hover:text-primary">{{ $company->phone }}</a>@endif
                        @if ($company->email)<a href="mailto:{{ $company->email }}" class="mt-1 block font-medium break-all text-ink hover:text-primary">{{ $company->email }}</a>@endif
                    </div>
                @endif
                @if ($company->fullAddress())
                    <div>
                        <p class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Alamat</p>
                        <p class="mt-2 leading-relaxed text-ink">{{ $company->fullAddress() }}</p>
                    </div>
                @endif
            </div>
            <x-site.social :company="$company" link-class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary" />
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'w-full') }}">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
        </div>
    </div>
</div>
