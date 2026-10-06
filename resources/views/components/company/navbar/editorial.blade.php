{{-- Navbar: Editorial — magazine masthead (date line, giant wordmark, nav between rules) that condenses into a compact sticky bar on scroll; compact on mobile. --}}
@php
    $wordmarkVw = round(min(11, ($ds->get('heading') === 'display-upper' ? 135 : 165) / max(mb_strlen($company->name), 8)), 2);
    $dateline = now()->translatedFormat('l, d F Y');
@endphp
<header x-data="siteNav" class="sticky top-0 z-50 text-ink lg:static">
    {{-- Mobile / compact bar --}}
    <div class="flex h-16 items-center justify-between gap-4 border-b border-line bg-surface/95 px-5 backdrop-blur-md sm:px-8 lg:hidden">
        <a href="{{ $site->home() }}" class="heading min-w-0 truncate text-xl">{{ $company->name }}</a>
        @include('components.company.navbar._toggle', ['class' => '-mr-2 text-ink hover:bg-ink/5'])
    </div>

    {{-- Desktop masthead --}}
    <div class="hidden bg-surface lg:block" x-data="{ compact: false }"
         x-init="const u = () => compact = window.scrollY > $el.offsetHeight; u(); window.addEventListener('scroll', u, { passive: true })">
        <div class="{{ $ds->container('wide') }}">
            <div class="flex h-10 items-center justify-between gap-6 border-b border-line text-[11px] font-medium tracking-[0.14em] text-muted uppercase">
                <span>{{ $dateline }}</span>
                <span class="hidden xl:inline">{{ collect([$company->city, $company->country])->filter()->implode(' — ') ?: $company->tagline }}</span>
                <span class="flex items-center gap-4">
                    @if ($company->established_year)<span>Sejak {{ $company->established_year }}</span>@endif
                    <a href="{{ $site->anchor('contact') }}" class="text-ink hover:text-primary">Hubungi Kami</a>
                </span>
            </div>
            <a href="{{ $site->home() }}" class="block py-7 text-center">
                <span class="heading block leading-[0.9]" style="font-size: clamp(1.75rem, {{ $wordmarkVw }}vw, 9rem)">{{ $company->name }}</span>
                @if ($company->tagline)
                    <span class="mt-4 block font-heading text-sm text-muted italic">{{ $company->tagline }}</span>
                @endif
            </a>
            <div class="border-t-2 border-b border-t-ink border-b-line">
                <nav class="flex h-12 items-center justify-center gap-9" aria-label="Menu utama">
                    @include('components.company.partials.nav-links', [
                        'linkClass' => 'text-xs font-semibold tracking-[0.18em] text-ink/75 uppercase transition hover:text-primary',
                        'activeClass' => '!text-primary',
                        'dropdownClass' => 'min-w-56 border border-line bg-card p-2 shadow-xl',
                        'childClass' => 'block px-3 py-2 text-sm text-muted hover:bg-surface-alt hover:text-ink',
                    ])
                </nav>
            </div>
        </div>

        {{-- Condensed sticky bar --}}
        <div x-cloak x-show="compact" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
             class="fixed inset-x-0 top-0 z-50 border-b border-line bg-surface/95 shadow-[0_10px_30px_-24px_rgb(0_0_0/0.5)] backdrop-blur-md">
            <div class="{{ $ds->container('wide') }} flex h-14 items-center justify-between gap-8">
                <a href="{{ $site->home() }}" class="heading min-w-0 truncate text-xl">{{ $company->name }}</a>
                <nav class="flex items-center gap-7" aria-label="Menu ringkas">
                    @include('components.company.partials.nav-links', [
                        'linkClass' => 'text-[11px] font-semibold tracking-[0.18em] text-ink/70 uppercase transition hover:text-primary',
                        'activeClass' => '!text-primary',
                        'dropdownClass' => 'min-w-56 border border-line bg-card p-2 shadow-xl',
                        'childClass' => 'block px-3 py-2 text-sm text-muted hover:bg-surface-alt hover:text-ink',
                    ])
                </nav>
            </div>
        </div>
    </div>

    @include('components.company.navbar._overlay')
</header>
