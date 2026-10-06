{{--
    Shared slide-in drawer for navbar variants (lives inside x-data="siteNav").
    Params: $hideClass (default 'lg:hidden'), $side ('right'|'left').
--}}
@php
    $hideClass ??= 'lg:hidden';
    $side ??= 'right';
@endphp
<div x-cloak x-show="open" class="fixed inset-0 z-50 {{ $hideClass }}" role="dialog" aria-modal="true" aria-label="Menu navigasi"
     x-effect="document.documentElement.style.overflow = open ? 'hidden' : ''" @keydown.escape.window="close()">
    <div x-show="open" x-transition.opacity.duration.300ms @click="close()" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div x-show="open"
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="{{ $side === 'left' ? '-translate-x-full' : 'translate-x-full' }}" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $side === 'left' ? '-translate-x-full' : 'translate-x-full' }}"
         class="absolute inset-y-0 {{ $side === 'left' ? 'left-0' : 'right-0' }} flex w-[min(24rem,88vw)] flex-col overflow-y-auto bg-surface text-ink shadow-2xl">
        <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-line px-5">
            <a href="{{ $site->home() }}" @click="close()" class="min-w-0 text-ink">
                <x-site.logo :company="$company" text-class="text-base font-bold tracking-tight" />
            </a>
            <button type="button" @click="close()" class="inline-flex size-11 shrink-0 items-center justify-center rounded-full text-ink hover:bg-surface-alt" aria-label="Tutup menu">
                <x-icon name="x" class="size-5" />
            </button>
        </div>
        <div class="flex-1 px-5 py-4">
            @include('components.company.partials.mobile-menu')
        </div>
        <div class="space-y-5 border-t border-line bg-surface-alt px-5 py-6 text-sm">
            @if ($company->phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="flex items-center gap-3 text-ink"><x-icon name="phone" class="size-4 text-primary" /> {{ $company->phone }}</a>
            @endif
            @if ($company->email)
                <a href="mailto:{{ $company->email }}" class="flex items-center gap-3 break-all text-ink"><x-icon name="mail" class="size-4 shrink-0 text-primary" /> {{ $company->email }}</a>
            @endif
            <x-site.social :company="$company" link-class="inline-flex size-10 items-center justify-center rounded-full border border-line text-muted transition hover:text-primary" />
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'w-full') }}">Hubungi Kami</a>
        </div>
    </div>
</div>
