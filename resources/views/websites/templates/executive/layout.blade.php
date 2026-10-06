<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-secondary font-body text-on-secondary/70 antialiased selection:bg-primary selection:text-on-primary">

    {{-- Header: transparent over the hero, solid navy with a gold hairline once scrolled --}}
    <header x-data="siteNav" class="fixed inset-x-0 top-0 z-40 transition duration-500" :class="scrolled || open ? 'bg-secondary/95 backdrop-blur shadow-[0_1px_0_0_color-mix(in_oklab,var(--brand-primary)_35%,transparent)]' : 'bg-transparent'">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-8 px-6 lg:h-24">
            <a href="{{ $site->home() }}" class="min-w-0 text-on-secondary">
                <x-site.logo :company="$company" text-class="text-xl font-semibold tracking-wide sm:text-2xl" img-class="h-10 w-auto" />
            </a>

            <nav class="hidden items-center gap-9 lg:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 py-2 text-[11px] font-medium tracking-[0.25em] uppercase {{ $item->active ? 'text-primary' : 'text-on-secondary/80 hover:text-primary' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3" />
                            </button>
                            <div x-cloak x-show="open" x-transition.opacity class="absolute top-full left-1/2 w-60 -translate-x-1/2 pt-4">
                                <div class="border border-primary/30 bg-secondary py-3 shadow-2xl shadow-black/40">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="block px-6 py-2.5 text-[11px] tracking-[0.2em] text-on-secondary/70 uppercase transition hover:pl-7 hover:text-primary">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="py-2 text-[11px] font-medium tracking-[0.25em] uppercase {{ $item->active ? 'text-primary' : 'text-on-secondary/80 hover:text-primary' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-4">
                <a href="{{ $site->anchor('contact') }}" class="hidden border border-primary px-6 py-3 text-[11px] font-medium tracking-[0.25em] text-primary uppercase transition hover:bg-primary hover:text-on-primary sm:inline-flex rounded-btn">Hubungi Kami</a>
                <button type="button" class="inline-flex size-11 items-center justify-center border border-on-secondary/20 text-on-secondary lg:hidden" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation: full-height navy panel with serif links --}}
        <div x-cloak x-show="open" x-transition.opacity class="h-[calc(100dvh-5rem)] overflow-y-auto border-t border-primary/30 bg-secondary lg:hidden">
            <nav class="mx-auto max-w-7xl px-6 py-8">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }" class="border-b border-on-secondary/10">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-4 font-heading text-3xl text-on-secondary">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-5 text-primary transition" ::class="sub && 'rotate-180'" />
                            </button>
                            <div x-show="sub" x-collapse class="pb-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-2 pl-4 text-xs tracking-[0.2em] text-on-secondary/70 uppercase">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block border-b border-on-secondary/10 py-4 font-heading text-3xl {{ $item->active ? 'text-primary' : 'text-on-secondary' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
                <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-8 flex justify-center bg-primary px-6 py-4 text-xs font-semibold tracking-[0.25em] text-on-primary uppercase rounded-btn">Hubungi Kami</a>
                @if ($company->phone)
                    <p class="mt-6 text-center text-sm text-on-secondary/60">{{ $company->phone }}</p>
                @endif
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer: dark, gold divider, multi-column --}}
    <footer class="relative bg-secondary">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative mx-auto max-w-7xl px-6">
            <div class="flex flex-col items-center py-16 text-center">
                <div class="text-on-secondary">
                    <x-site.logo :company="$company" text-class="text-3xl font-semibold tracking-wide" img-class="h-12 w-auto" />
                </div>
                @if ($company->tagline)
                    <p class="mt-4 font-heading text-xl text-on-secondary/70 italic">{{ $company->tagline }}</p>
                @endif
                <div class="mt-10 flex w-full max-w-md items-center gap-4">
                    <span class="h-px flex-1 bg-gradient-to-r from-transparent to-primary"></span>
                    <span class="size-1.5 rotate-45 bg-primary"></span>
                    <span class="h-px flex-1 bg-gradient-to-l from-transparent to-primary"></span>
                </div>
            </div>

            <div class="grid gap-12 pb-16 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h3 class="text-[11px] font-medium tracking-[0.3em] text-primary uppercase">Perusahaan</h3>
                    <p class="mt-5 leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 170) }}</p>
                </div>
                <div>
                    <h3 class="text-[11px] font-medium tracking-[0.3em] text-primary uppercase">Navigasi</h3>
                    <ul class="mt-5 space-y-3">
                        @foreach ($menu->take(7) as $item)
                            @if ($item->url)
                                <li><a {!! $item->attributes() !!} class="transition hover:text-primary">{{ $item->title }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3 class="text-[11px] font-medium tracking-[0.3em] text-primary uppercase">Layanan</h3>
                    <ul class="mt-5 space-y-3">
                        @foreach ($company->services->take(6) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="transition hover:text-primary">{{ $service->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3 class="text-[11px] font-medium tracking-[0.3em] text-primary uppercase">Kantor</h3>
                    <ul class="mt-5 space-y-3">
                        @if ($company->fullAddress())<li class="leading-relaxed">{{ $company->fullAddress() }}</li>@endif
                        @if ($company->phone)<li><a href="tel:{{ $company->phone }}" class="hover:text-primary">{{ $company->phone }}</a></li>@endif
                        @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="break-all hover:text-primary">{{ $company->email }}</a></li>@endif
                        @if ($company->working_hours)<li>{{ $company->working_hours }}</li>@endif
                    </ul>
                    <x-site.social :company="$company" class="mt-6" icon-class="size-3.5" link-class="inline-flex size-9 items-center justify-center border border-primary/40 text-primary transition hover:bg-primary hover:text-on-primary" />
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-primary/20 py-7 text-[11px] tracking-[0.2em] text-on-secondary/50 uppercase sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $company->name }}</p>
                <div class="flex flex-wrap gap-6">
                    @foreach ($pages->take(4) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="hover:text-primary">{{ $p->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @include('websites.partials.preview-bar')
</body>
</html>
