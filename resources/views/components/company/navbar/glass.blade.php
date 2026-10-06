{{-- Navbar: Glass — compact sticky translucent bar (backdrop blur) with a scroll-spy pill highlighting the active section. --}}
<header x-data="siteNav" class="sticky top-0 z-50 text-ink">
    <div class="border-b border-line bg-card/60 backdrop-blur-xl backdrop-saturate-150 transition-[background-color,box-shadow] duration-300"
         :class="scrolled && '!bg-card/75 shadow-[0_10px_40px_-20px_rgb(0_0_0/0.45)]'">
        <div class="{{ $ds->container('wide') }} flex h-16 items-center justify-between gap-6">
            <a href="{{ $site->home() }}" class="min-w-0 text-ink">
                <x-site.logo :company="$company" img-class="h-8 w-auto" text-class="text-base font-bold tracking-tight" />
            </a>
            <nav class="hidden items-center gap-0.5 rounded-full border border-line bg-surface/40 p-1 lg:flex" aria-label="Menu utama"
                 x-data="{ cur: '' }"
                 x-init="const ids = [...$el.querySelectorAll('a[href*=\'#\']')].map(a => a.hash.slice(1)).filter(Boolean);
                         if ('IntersectionObserver' in window) { const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) cur = e.target.id }), { rootMargin: '-45% 0px -50% 0px' }); ids.forEach(id => { const s = document.getElementById(id); if (s) io.observe(s) }) }">
                @foreach ($menu as $item)
                    @php($hash = $item->url ? (string) parse_url($item->url, PHP_URL_FRAGMENT) : '')
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ dd: false }" @mouseenter="dd = true" @mouseleave="dd = false" @keydown.escape="dd = false">
                            <button type="button" @click="dd = !dd" :aria-expanded="dd"
                                    class="inline-flex items-center gap-1 rounded-full px-3.5 py-1.5 text-[13px] font-medium transition {{ $item->active ? 'bg-primary/15 text-primary' : 'text-ink/70 hover:text-ink' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3 transition" ::class="dd && 'rotate-180'" />
                            </button>
                            <div x-cloak x-show="dd" x-transition.origin.top class="absolute top-full left-1/2 z-50 -translate-x-1/2 pt-3">
                                <div class="min-w-56 rounded-2xl border border-line bg-card/90 p-1.5 shadow-2xl backdrop-blur-xl">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="block rounded-xl px-3.5 py-2 text-sm text-muted transition hover:bg-ink/5 hover:text-ink">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!}
                           class="rounded-full px-3.5 py-1.5 text-[13px] font-medium transition {{ $item->active ? 'bg-primary/15 text-primary' : 'text-ink/70 hover:text-ink' }}"
                           @if ($hash) :class="cur === '{{ $hash }}' && '!bg-primary/15 !text-primary'" @endif>{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-sm:hidden! !px-4 !py-2 text-[13px]!') }}">Hubungi Kami</a>
                <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Menu"
                        class="-mr-1.5 inline-flex size-11 items-center justify-center rounded-full text-ink transition hover:bg-ink/5 lg:hidden">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>
    </div>
    <div x-cloak x-show="open" x-collapse class="border-b border-line bg-card/90 backdrop-blur-xl lg:hidden">
        <div class="{{ $ds->container() }} max-h-[calc(100dvh-4rem)] overflow-y-auto pt-2 pb-6">
            @include('components.company.partials.mobile-menu')
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'mt-4 w-full') }}">Hubungi Kami</a>
        </div>
    </div>
</header>
