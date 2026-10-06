{{-- Navbar: Mega — corporate white bar with a utility row; items with children open a full-width mega panel (columns + promo). Mobile accordion drawer. --}}
<header x-data="siteNav" class="sticky top-0 z-50 bg-surface text-ink md:-top-9">
    <div class="hidden border-b border-line bg-surface-alt md:block">
        <div class="{{ $ds->container('wide') }} flex h-9 items-center justify-between gap-6 text-xs text-muted">
            <p class="truncate">{{ $company->tagline ?: $company->name }}</p>
            <div class="flex shrink-0 items-center gap-5">
                @foreach ($pages->take(3) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink {{ $site->isCurrentPage($p->slug) ? 'text-ink' : '' }}">{{ $p->title }}</a>
                @endforeach
                @if ($company->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="inline-flex items-center gap-1.5 transition hover:text-ink"><x-icon name="phone" class="size-3.5" />{{ $company->phone }}</a>
                @endif
                <a href="{{ $site->anchor('contact') }}" class="font-semibold text-primary hover:underline">Kontak</a>
            </div>
        </div>
    </div>

    <div class="relative border-b border-line transition-shadow" :class="scrolled && 'shadow-[0_12px_30px_-22px_rgb(0_0_0/0.45)]'">
        <div class="{{ $ds->container('wide') }} flex h-16 items-stretch justify-between gap-8 lg:h-20">
            <a href="{{ $site->home() }}" class="flex min-w-0 items-center text-ink">
                <x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" />
            </a>
            <nav class="hidden items-stretch gap-1 lg:flex" aria-label="Menu utama">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="flex" x-data="{ mega: false }" @mouseenter="mega = true" @mouseleave="mega = false" @keydown.escape="mega = false">
                            <button type="button" @click="mega = !mega" :aria-expanded="mega"
                                    class="relative inline-flex items-center gap-1 px-3 text-sm font-semibold transition hover:text-primary {{ $item->active ? 'text-primary' : 'text-ink/80' }}"
                                    :class="mega && '!text-primary'">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5 transition" ::class="mega && 'rotate-180'" />
                                <span class="absolute inset-x-3 bottom-0 h-0.5 origin-left bg-primary transition-transform duration-300" :class="mega ? 'scale-x-100' : 'scale-x-0'"></span>
                            </button>
                            <div x-cloak x-show="mega" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                                 x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
                                 class="absolute inset-x-0 top-full border-y border-line bg-surface shadow-[0_30px_60px_-30px_rgb(0_0_0/0.35)]">
                                <div class="{{ $ds->container('wide') }} grid grid-cols-12 gap-10 py-10">
                                    <div class="col-span-3">
                                        <p class="text-xs font-semibold tracking-[0.18em] text-primary uppercase">{{ $company->name }}</p>
                                        <p class="heading mt-3 text-3xl">{{ $item->title }}</p>
                                        @if ($item->url)
                                            <a {!! $item->attributes() !!} class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-ink hover:text-primary">Lihat semua <x-icon name="arrow-right" class="size-4" /></a>
                                        @endif
                                    </div>
                                    <div class="col-span-6 grid content-start gap-x-8 gap-y-1 border-l border-line pl-10 grid-cols-2">
                                        @foreach ($item->children as $child)
                                            <a {!! $child->attributes() !!} class="group flex items-center justify-between gap-4 rounded-brand px-4 py-3.5 transition hover:bg-surface-alt">
                                                <span class="font-medium text-ink">{{ $child->title }}</span>
                                                <x-icon name="arrow-right" class="size-4 -translate-x-1 text-primary opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100" />
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="col-span-3">
                                        <div class="overflow-hidden rounded-brand border border-line bg-surface-alt">
                                            <x-site.img :src="$company->url('hero_image')" :alt="$company->name" class="aspect-[16/9] w-full object-cover" />
                                            <div class="p-5">
                                                <p class="text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->tagline ?: $company->description, 110) }}</p>
                                                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'mt-4 w-full !py-2.5') }}">Hubungi Kami</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="relative inline-flex items-center px-3 text-sm font-semibold transition hover:text-primary {{ $item->active ? 'text-primary' : 'text-ink/80' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-sm:hidden! !py-2.5') }}">Hubungi Kami</a>
                @include('components.company.navbar._toggle', ['class' => '-mr-2 text-ink hover:bg-ink/5 lg:hidden'])
            </div>
        </div>
    </div>
    @include('components.company.navbar._drawer')
</header>
