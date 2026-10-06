<!DOCTYPE html>
<html lang="id" class="scroll-smooth bg-slate-950">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-slate-950 font-body text-slate-400 antialiased selection:bg-primary selection:text-on-primary">

    {{-- Glassy floating sticky navigation --}}
    <header x-data="siteNav" class="sticky top-0 z-40 px-3 pt-3 sm:px-4 sm:pt-4">
        <div class="mx-auto max-w-7xl rounded-brand border transition duration-300"
             :class="scrolled || open ? 'border-white/10 bg-slate-900/70 shadow-2xl shadow-black/40 backdrop-blur-xl' : 'border-white/5 bg-slate-900/30 backdrop-blur-md'">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-5">
                <a href="{{ $site->home() }}" class="text-white">
                    <x-site.logo :company="$company" text-class="text-base font-bold tracking-tight" img-class="h-8 w-auto" />
                </a>

                <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Main">
                    @foreach ($menu as $item)
                        @if ($item->hasChildren())
                            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <button type="button" @click="open = !open" class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium transition {{ $item->active ? 'text-white' : 'text-slate-400 hover:text-white' }}">
                                    {{ $item->title }} <x-icon name="chevron-down" class="size-3.5 opacity-60" />
                                </button>
                                <div x-cloak x-show="open" x-transition.origin.top class="absolute left-0 top-full w-64 pt-3">
                                    <div class="rounded-brand border border-white/10 bg-slate-900/95 p-1.5 shadow-2xl shadow-black/50 backdrop-blur-xl">
                                        @foreach ($item->children as $child)
                                            <a {!! $child->attributes() !!} class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">
                                                <span class="font-mono text-[10px] text-primary/70">0{{ $loop->iteration }}</span>
                                                {{ $child->title }}
                                                <x-icon name="arrow-right" class="ml-auto size-3.5 -translate-x-1 opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100" />
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a {!! $item->attributes() !!} class="relative rounded-lg px-3 py-2 text-sm font-medium transition {{ $item->active ? 'text-white' : 'text-slate-400 hover:text-white' }}">
                                {{ $item->title }}
                                @if ($item->active)<span class="absolute inset-x-3 -bottom-px h-px bg-linear-to-r from-transparent via-primary to-transparent"></span>@endif
                            </a>
                        @endif
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-2 rounded-btn bg-primary px-4 py-2 text-sm font-semibold text-on-primary shadow-[0_0_24px_-4px_var(--brand-primary)] transition hover:shadow-[0_0_32px_-2px_var(--brand-primary)] sm:inline-flex">
                        Hubungi Kami <x-icon name="arrow-right" class="size-4" />
                    </a>
                    <button type="button" class="inline-flex size-10 items-center justify-center rounded-lg border border-white/10 text-slate-200 lg:hidden" @click="open = !open" aria-label="Menu">
                        <x-icon name="menu" class="size-5" x-show="!open" />
                        <x-icon name="x" class="size-5" x-show="open" x-cloak />
                    </button>
                </div>
            </div>

            {{-- Mobile navigation --}}
            <div x-cloak x-show="open" x-collapse class="lg:hidden">
                <nav class="space-y-0.5 border-t border-white/10 p-3">
                    @foreach ($menu as $item)
                        @if ($item->hasChildren())
                            <div x-data="{ sub: false }">
                                <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-slate-200 hover:bg-white/5">
                                    <span><span class="mr-2 font-mono text-xs text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $item->title }}</span>
                                    <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                                </button>
                                <div x-show="sub" x-collapse class="ml-5 border-l border-white/10 pl-3">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} @click="close()" class="block rounded-lg px-3 py-2 text-sm text-slate-400 hover:text-white">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a {!! $item->attributes() !!} @click="close()" class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ $item->active ? 'bg-white/5 text-white' : 'text-slate-200 hover:bg-white/5' }}"><span class="mr-2 font-mono text-xs text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $item->title }}</a>
                        @endif
                    @endforeach
                    <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-2 flex items-center justify-center gap-2 rounded-btn bg-primary px-4 py-3 text-sm font-semibold text-on-primary">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
                </nav>
            </div>
        </div>
    </header>

    <main class="-mt-20 sm:-mt-20">
        @yield('content')
    </main>

    {{-- Footer with giant wordmark --}}
    <footer class="relative overflow-hidden border-t border-white/10 bg-slate-950">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.04)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent_80%)]"></div>
        <div class="relative mx-auto max-w-7xl px-5 pt-20 sm:px-6">
            <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <div class="text-white"><x-site.logo :company="$company" text-class="text-lg font-bold" img-class="h-9 w-auto" /></div>
                    <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 160) }}</p>
                    <p class="mt-6 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/5 px-3 py-1 font-mono text-xs text-emerald-300">
                        <span class="relative flex size-2"><span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-60"></span><span class="relative inline-flex size-2 rounded-full bg-emerald-400"></span></span>
                        Semua sistem beroperasi normal
                    </p>
                </div>
                <div class="lg:col-span-2">
                    <h3 class="font-mono text-xs tracking-wider text-primary uppercase">// navigasi</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($menu->take(7) as $item)
                            @if ($item->url)
                                <li><a {!! $item->attributes() !!} class="transition hover:text-white">{{ $item->title }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="lg:col-span-3">
                    <h3 class="font-mono text-xs tracking-wider text-primary uppercase">// layanan</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @forelse ($company->services->take(5) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="transition hover:text-white">{{ $service->title }}</a></li>
                        @empty
                            @foreach ($pages->take(5) as $p)
                                <li><a href="{{ $site->page($p->slug) }}" class="transition hover:text-white">{{ $p->title }}</a></li>
                            @endforeach
                        @endforelse
                    </ul>
                </div>
                <div class="lg:col-span-3">
                    <h3 class="font-mono text-xs tracking-wider text-primary uppercase">// kontak</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="transition hover:text-white">{{ $company->email }}</a></li>@endif
                        @if ($company->phone)<li><a href="tel:{{ $company->phone }}" class="transition hover:text-white">{{ $company->phone }}</a></li>@endif
                        @if ($company->fullAddress())<li class="leading-relaxed">{{ $company->fullAddress() }}</li>@endif
                    </ul>
                    <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-9 items-center justify-center rounded-lg border border-white/10 text-slate-400 transition hover:border-primary/50 hover:text-primary" />
                </div>
            </div>

            <div class="mt-16 flex flex-col items-start justify-between gap-4 border-t border-white/10 py-6 font-mono text-xs sm:flex-row sm:items-center">
                <p>&copy; {{ date('Y') }} {{ $company->name }}</p>
                <div class="flex flex-wrap gap-x-5 gap-y-2">
                    @foreach ($pages->take(4) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="transition hover:text-white">{{ $p->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pointer-events-none relative -mb-[0.18em] overflow-hidden px-4 text-center select-none" aria-hidden="true">
            <p class="font-heading text-[16vw] leading-[0.8] font-bold tracking-tighter whitespace-nowrap text-transparent bg-linear-to-b from-white/15 to-white/0 bg-clip-text lg:text-[13rem]">
                {{ \Illuminate\Support\Str::of($company->name)->replace(['PT. ', 'CV. ', 'PT ', 'CV '], '')->explode(' ')->first() }}
            </p>
        </div>
    </footer>

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
