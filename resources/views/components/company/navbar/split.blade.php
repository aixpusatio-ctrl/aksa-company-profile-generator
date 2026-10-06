{{-- Navbar: Split — menu divided left and right around a centered logo; refined fashion/boutique feel; collapsing mobile panel. --}}
@php
    $half = (int) ceil($menu->count() / 2);
    $leftMenu = $menu->take($half);
    $rightMenu = $menu->slice($half);
    $splitLink = 'text-[11px] font-semibold tracking-[0.24em] text-ink/70 uppercase transition hover:text-ink';
@endphp
<header x-data="siteNav" class="sticky top-0 z-50 border-b border-line bg-surface/95 text-ink backdrop-blur-md transition-shadow" :class="scrolled && 'shadow-[0_10px_30px_-24px_rgb(0_0_0/0.5)]'">
    <div class="{{ $ds->container('wide') }} grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-6 transition-[height] duration-300 lg:h-24" :class="scrolled && 'lg:!h-18'">
        <nav class="hidden items-center gap-8 lg:flex" aria-label="Menu utama (kiri)">
            @include('components.company.partials.nav-links', ['menu' => $leftMenu, 'linkClass' => $splitLink, 'activeClass' => '!text-ink underline decoration-primary underline-offset-8'])
        </nav>
        <button type="button" @click="open = !open" :aria-expanded="open" class="-ml-2 inline-flex h-11 items-center gap-2 px-2 text-[11px] font-semibold tracking-[0.24em] uppercase lg:hidden">
            <span class="relative block h-2.5 w-5">
                <span class="absolute inset-x-0 top-0 h-px bg-current transition" :class="open && 'translate-y-[5px] rotate-45'"></span>
                <span class="absolute inset-x-0 bottom-0 h-px bg-current transition" :class="open && '-translate-y-[4px] -rotate-45'"></span>
            </span>
            <span x-text="open ? 'Tutup' : 'Menu'">Menu</span>
        </button>
        <a href="{{ $site->home() }}" class="flex min-w-0 justify-center text-ink">
            <x-site.logo :company="$company" text-class="hidden text-lg font-semibold tracking-tight sm:inline lg:text-xl" />
        </a>
        <div class="flex items-center justify-end gap-8">
            <nav class="hidden items-center gap-8 lg:flex" aria-label="Menu utama (kanan)">
                @include('components.company.partials.nav-links', ['menu' => $rightMenu, 'linkClass' => $splitLink, 'activeClass' => '!text-ink underline decoration-primary underline-offset-8'])
            </nav>
            <a href="{{ $site->anchor('contact') }}" class="-mr-2 inline-flex size-11 items-center justify-center text-ink lg:hidden" aria-label="Hubungi Kami"><x-icon name="mail" class="size-5" /></a>
        </div>
    </div>
    <div x-cloak x-show="open" x-collapse class="border-t border-line lg:hidden">
        <div class="{{ $ds->container() }} max-h-[calc(100dvh-4rem)] overflow-y-auto py-6 text-center">
            <nav class="flex flex-col items-center" aria-label="Mobile">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }" class="w-full">
                            <button type="button" @click="sub = !sub" class="heading inline-flex items-center gap-2 py-2.5 text-2xl">{{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" /></button>
                            <div x-show="sub" x-collapse class="pb-2">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-1.5 text-sm tracking-[0.12em] text-muted uppercase">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="heading py-2.5 text-2xl {{ $item->active ? 'text-primary' : '' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="mx-auto mt-6 h-px w-12 bg-line"></div>
            <x-site.social :company="$company" class="mt-6 justify-center" link-class="inline-flex size-11 items-center justify-center rounded-full text-muted transition hover:text-ink" />
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'mt-4 w-full') }}">Hubungi Kami</a>
        </div>
    </div>
</header>
