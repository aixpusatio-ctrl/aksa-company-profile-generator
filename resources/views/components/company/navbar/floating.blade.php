{{-- Navbar: Floating — pill-shaped bar detached from the edges, centered links, CTA pill; mobile dropdown card under the pill. --}}
@php($overlay = $heroFirst ?? false)
<header x-data="siteNav" @keydown.escape.window="close()" @click.outside="close()"
        class="{{ $overlay ? 'fixed inset-x-0 top-0' : 'sticky top-0' }} z-50 px-3 pt-3 sm:px-5 sm:pt-4">
    <div class="mx-auto w-full max-w-6xl">
        <div class="flex h-14 items-center justify-between gap-3 rounded-full border border-line bg-card/80 py-1.5 pr-1.5 pl-4 shadow-[0_8px_30px_-16px_rgb(0_0_0/0.3)] backdrop-blur-xl backdrop-saturate-150 transition-shadow duration-300 sm:h-16 sm:pl-6 lg:grid lg:grid-cols-[1fr_auto_1fr]"
             :class="scrolled && 'shadow-[0_18px_40px_-18px_rgb(0_0_0/0.4)]'">
            <a href="{{ $site->home() }}" class="min-w-0 justify-self-start text-ink">
                <x-site.logo :company="$company" img-class="h-8 w-auto" text-class="text-base font-bold tracking-tight" />
            </a>
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Menu utama">
                @include('components.company.partials.nav-links', [
                    'linkClass' => 'rounded-full px-3.5 py-2 text-sm font-medium text-ink/70 transition hover:bg-ink/5 hover:text-ink',
                    'activeClass' => 'bg-ink/5 !text-ink',
                    'dropdownClass' => 'min-w-56 rounded-3xl border border-line bg-card p-2 shadow-2xl',
                    'childClass' => 'block rounded-2xl px-4 py-2.5 text-sm text-muted hover:bg-surface-alt hover:text-ink',
                ])
            </nav>
            <div class="flex items-center justify-self-end gap-1.5">
                <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary transition hover:brightness-110 sm:inline-flex">
                    Hubungi Kami <x-icon name="arrow-right" class="size-4" />
                </a>
                <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Menu"
                        class="inline-flex size-11 items-center justify-center rounded-full bg-ink/5 text-ink transition hover:bg-ink/10 lg:hidden">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mt-2 max-h-[calc(100dvh-6rem)] overflow-y-auto rounded-[1.75rem] border border-line bg-card p-5 text-ink shadow-2xl lg:hidden">
            @include('components.company.partials.mobile-menu')
            <div class="mt-4 flex items-center justify-between gap-4">
                <x-site.social :company="$company" link-class="inline-flex size-10 items-center justify-center rounded-full bg-surface-alt text-muted transition hover:text-primary" />
            </div>
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-primary px-5 py-3.5 text-sm font-semibold text-on-primary">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
        </div>
    </div>
</header>
