{{-- Navbar: Classic — logo left, links center/right, CTA, sticky white bar. --}}
<header x-data="siteNav" class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur transition" :class="scrolled && 'shadow-sm'">
    <div class="{{ $ds->container() }} flex h-18 items-center justify-between gap-6">
        <a href="{{ $site->home() }}" class="min-w-0 text-ink">
            <x-site.logo :company="$company" text-class="line-clamp-2 text-base font-bold tracking-tight sm:text-lg" />
        </a>
        <nav class="hidden items-center gap-7 lg:flex" aria-label="Main">
            @include('components.company.partials.nav-links')
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-sm:hidden! !py-2.5') }}">Hubungi Kami</a>
            <button type="button" class="inline-flex size-11 items-center justify-center rounded-md text-ink lg:hidden" @click="open = !open" :aria-expanded="open" aria-label="Menu">
                <x-icon name="menu" class="size-6" x-show="!open" />
                <x-icon name="x" class="size-6" x-show="open" x-cloak />
            </button>
        </div>
    </div>
    <div x-cloak x-show="open" x-collapse class="border-t border-line bg-surface lg:hidden">
        <div class="{{ $ds->container() }} pt-2 pb-6">
            @include('components.company.partials.mobile-menu')
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'mt-4 w-full') }}">Hubungi Kami</a>
        </div>
    </div>
</header>
