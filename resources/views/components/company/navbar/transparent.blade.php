{{-- Navbar: Transparent — floats over the hero with white text, turns into a solid bar on scroll; full-screen mobile menu. --}}
@php($overlay = $heroFirst ?? false)
<header x-data="siteNav" class="{{ $overlay ? 'fixed inset-x-0 top-0' : 'sticky top-0' }} z-50">
    <div @if ($overlay)
             class="relative transition-[background-color,box-shadow,color] duration-300"
             :class="scrolled ? 'bg-surface/95 text-ink shadow-[0_12px_32px_-20px_rgb(0_0_0/0.45)] backdrop-blur-md' : 'text-white'"
         @else
             class="relative border-b border-line bg-surface/95 text-ink backdrop-blur-md transition-shadow duration-300"
             :class="scrolled && 'shadow-[0_12px_32px_-20px_rgb(0_0_0/0.35)]'"
         @endif>
        @if ($overlay)
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-36 bg-linear-to-b from-black/50 via-black/20 to-transparent transition-opacity duration-300" :class="scrolled && 'opacity-0'"></div>
        @endif
        <div class="{{ $ds->container('wide') }} flex items-center justify-between gap-6 transition-[height] duration-300 {{ $overlay ? 'h-20' : 'h-18' }}" @if ($overlay) :class="scrolled ? 'h-16' : 'h-20'" @endif>
            <a href="{{ $site->home() }}" class="min-w-0 shrink-0">
                <x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" />
            </a>
            <nav class="hidden items-center gap-8 lg:flex" aria-label="Menu utama">
                @include('components.company.partials.nav-links', [
                    'linkClass' => 'relative py-2 text-sm font-medium opacity-80 transition hover:opacity-100',
                    'activeClass' => 'opacity-100 underline decoration-primary decoration-2 underline-offset-8',
                ])
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-sm:hidden! !py-2.5') }}">Hubungi Kami</a>
                @include('components.company.navbar._toggle', ['class' => 'text-current hover:bg-current/10 lg:hidden'])
            </div>
        </div>
    </div>
    @include('components.company.navbar._overlay')
</header>
