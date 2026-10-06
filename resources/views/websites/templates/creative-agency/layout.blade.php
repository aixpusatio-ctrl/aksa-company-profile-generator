<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-neutral-100 font-body text-neutral-800 antialiased selection:bg-primary selection:text-on-primary">

    <header x-data="siteNav" class="sticky top-0 z-40 px-3 pt-3 sm:px-5" @keydown.escape.window="close()">
        <div class="mx-auto flex max-w-[90rem] items-center justify-between gap-4 rounded-full border border-black/5 bg-white/80 py-2 pr-2 pl-5 backdrop-blur-xl transition" :class="scrolled && 'shadow-lg shadow-black/5'">
            <a href="{{ $site->home() }}" class="text-neutral-950">
                <x-site.logo :company="$company" text-class="text-lg font-extrabold tracking-tight" img-class="h-8 w-auto" />
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1 rounded-full px-4 py-2 text-sm font-semibold transition {{ $item->active ? 'bg-neutral-950 text-white' : 'text-neutral-700 hover:bg-neutral-950/5' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5" />
                            </button>
                            <div x-cloak x-show="open" x-transition class="absolute left-0 top-full w-60 pt-3">
                                <div class="rounded-3xl bg-neutral-950 p-2 shadow-2xl">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold text-white/80 transition hover:bg-primary hover:text-on-primary">{{ $child->title }} <x-icon name="arrow-up-right" class="size-4" /></a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $item->active ? 'bg-neutral-950 text-white' : 'text-neutral-700 hover:bg-neutral-950/5' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="group hidden items-center gap-2 rounded-btn bg-primary py-2.5 pr-2.5 pl-5 text-sm font-bold text-on-primary transition hover:scale-[1.03] sm:inline-flex">
                    Let's talk
                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-black/15 transition group-hover:rotate-45"><x-icon name="arrow-up-right" class="size-4" /></span>
                </a>
                <button type="button" @click="open = true" class="inline-flex items-center gap-2 rounded-full bg-neutral-950 px-4 py-2.5 text-sm font-bold text-white lg:hidden" aria-label="Buka menu">
                    Menu <x-icon name="menu" class="size-4" />
                </button>
            </div>
        </div>

        {{-- Full-screen off-canvas menu --}}
        <div x-cloak x-show="open"
             x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-300 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="fixed inset-0 z-50 flex flex-col overflow-y-auto bg-secondary text-on-secondary lg:hidden">
            <div class="flex items-center justify-between px-6 py-5">
                <x-site.logo :company="$company" text-class="text-lg font-extrabold" img-class="h-8 w-auto brightness-0 invert" />
                <button type="button" @click="close()" class="inline-flex size-12 items-center justify-center rounded-full bg-primary text-on-primary" aria-label="Tutup menu"><x-icon name="x" class="size-6" /></button>
            </div>
            <nav class="flex-1 px-6 py-6">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }" class="border-b border-white/10">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-4 text-left font-heading text-4xl font-extrabold tracking-tight">
                                {{ $item->title }} <x-icon name="plus" class="size-7 text-primary transition" ::class="sub && 'rotate-45'" />
                            </button>
                            <div x-show="sub" x-collapse class="pb-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-2 text-lg font-semibold text-on-secondary/70">↳ {{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="flex items-center justify-between border-b border-white/10 py-4 font-heading text-4xl font-extrabold tracking-tight transition hover:text-primary">{{ $item->title }} <x-icon name="arrow-up-right" class="size-7 opacity-40" /></a>
                    @endif
                @endforeach
            </nav>
            <div class="space-y-4 px-6 pb-10">
                <a href="{{ $site->anchor('contact') }}" @click="close()" class="flex items-center justify-center gap-2 rounded-btn bg-primary px-6 py-4 font-bold text-on-primary">Let's talk <x-icon name="arrow-up-right" class="size-5" /></a>
                <x-site.social :company="$company" class="justify-center" link-class="inline-flex size-11 items-center justify-center rounded-full border border-white/20 transition hover:bg-white hover:text-neutral-950" />
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-secondary text-on-secondary">
        <div class="mx-auto max-w-[90rem] px-6 pt-20 sm:px-10">
            <p class="text-sm font-semibold tracking-widest text-on-secondary/50 uppercase">Punya ide liar? ✦ Ceritakan pada kami</p>
            <a href="{{ $company->email ? 'mailto:'.$company->email : $site->anchor('contact') }}" class="group mt-4 flex items-center gap-4 font-heading text-[15vw] leading-[0.85] font-extrabold tracking-tighter whitespace-nowrap lg:text-[12rem]">
                <span class="transition group-hover:text-primary">Let's talk</span>
                <span class="inline-flex size-[12vw] shrink-0 items-center justify-center rounded-full bg-primary text-on-primary transition duration-500 group-hover:rotate-45 lg:size-40">
                    <x-icon name="arrow-up-right" class="size-1/2" />
                </span>
            </a>

            <div class="mt-20 grid gap-10 border-t border-white/10 py-12 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <x-site.logo :company="$company" text-class="text-lg font-extrabold" img-class="h-9 w-auto brightness-0 invert" />
                    @if ($company->tagline)<p class="mt-4 max-w-xs text-sm text-on-secondary/60">{{ $company->tagline }}</p>@endif
                </div>
                <div>
                    <p class="text-xs font-bold tracking-widest text-on-secondary/40 uppercase">Studio</p>
                    <p class="mt-4 text-sm leading-relaxed text-on-secondary/80">{{ $company->fullAddress() ?: ($company->city ?: 'Indonesia') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold tracking-widest text-on-secondary/40 uppercase">Say hello</p>
                    <div class="mt-4 space-y-1 text-sm">
                        @if ($company->email)<a href="mailto:{{ $company->email }}" class="block break-all text-on-secondary/80 hover:text-primary">{{ $company->email }}</a>@endif
                        @if ($company->phone)<a href="tel:{{ $company->phone }}" class="block text-on-secondary/80 hover:text-primary">{{ $company->phone }}</a>@endif
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold tracking-widest text-on-secondary/40 uppercase">Follow</p>
                    <x-site.social :company="$company" class="mt-4" link-class="inline-flex size-10 items-center justify-center rounded-full bg-white/10 transition hover:-rotate-12 hover:bg-primary hover:text-on-primary" />
                </div>
            </div>
            <div class="flex flex-col gap-4 border-t border-white/10 py-6 text-xs text-on-secondary/50 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ date('Y') }} {{ $company->name }} — dibuat dengan ♥ & kopi.</p>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    @foreach ($menu->take(6) as $item)
                        @if ($item->url)<a {!! $item->attributes() !!} class="hover:text-on-secondary">{{ $item->title }}</a>@endif
                    @endforeach
                    @foreach ($pages->take(3) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="hover:text-on-secondary">{{ $p->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
