<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-white font-body text-slate-600 antialiased">

    {{-- Utility bar --}}
    <div class="bg-secondary text-on-secondary/75 text-xs">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-2">
            <div class="flex min-w-0 items-center gap-5">
                @if ($company->working_hours)
                    <span class="hidden items-center gap-1.5 sm:flex"><x-icon name="clock" class="size-3.5 text-primary" /> {{ $company->working_hours }}</span>
                @endif
                @if ($company->city)
                    <span class="hidden items-center gap-1.5 md:flex"><x-icon name="map-pin" class="size-3.5 text-primary" /> Pabrik {{ $company->city }}</span>
                @endif
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="flex min-w-0 items-center gap-1.5 truncate hover:text-on-secondary"><x-icon name="mail" class="size-3.5 shrink-0 text-primary" /> <span class="truncate">{{ $company->email }}</span></a>
                @endif
            </div>
            <div class="flex shrink-0 items-center gap-4">
                @if ($company->phone)
                    <a href="tel:{{ $company->phone }}" class="flex items-center gap-1.5 font-mono font-semibold text-on-secondary hover:text-primary"><x-icon name="phone" class="size-3.5" /> {{ $company->phone }}</a>
                @endif
                <x-site.social :company="$company" class="hidden lg:flex" icon-class="size-3.5" link-class="inline-flex size-6 items-center justify-center hover:text-on-secondary" />
            </div>
        </div>
    </div>

    {{-- Main navigation --}}
    <header x-data="siteNav" class="sticky top-0 z-40 border-b border-slate-200 bg-white" :class="scrolled && 'shadow-md shadow-slate-900/5'">
        <div class="mx-auto flex h-18 max-w-7xl items-stretch justify-between gap-6 px-6">
            <a href="{{ $site->home() }}" class="flex items-center text-slate-900">
                <x-site.logo :company="$company" text-class="text-base font-bold tracking-tight uppercase" />
            </a>

            <nav class="hidden items-stretch lg:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative flex" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1 border-b-2 px-4 text-[13px] font-semibold tracking-wide uppercase transition {{ $item->active ? 'border-primary text-slate-900' : 'border-transparent text-slate-600 hover:border-primary hover:text-slate-900' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5" />
                            </button>
                            <div x-cloak x-show="open" x-transition.opacity class="absolute left-0 top-full w-60">
                                <div class="border border-slate-200 border-t-2 border-t-primary bg-white py-1 shadow-xl shadow-slate-900/10">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary">{{ $child->title }} <x-icon name="chevron-right" class="size-3.5 opacity-40" /></a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="flex items-center border-b-2 px-4 text-[13px] font-semibold tracking-wide uppercase transition {{ $item->active ? 'border-primary text-slate-900' : 'border-transparent text-slate-600 hover:border-primary hover:text-slate-900' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-2 rounded-btn bg-primary px-5 py-3 text-xs font-bold tracking-wider text-on-primary uppercase transition hover:opacity-90 sm:inline-flex">
                    <x-icon name="clipboard" class="size-4" /> Request Quote
                </a>
                <button type="button" class="inline-flex size-10 items-center justify-center border border-slate-200 text-slate-700 lg:hidden" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-t border-slate-200 bg-white lg:hidden">
            <nav class="divide-y divide-slate-100 px-6 py-2">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-3 text-sm font-semibold tracking-wide text-slate-800 uppercase">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                            </button>
                            <div x-show="sub" x-collapse class="mb-3 border-l-2 border-primary pl-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-2 text-sm text-slate-600">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block py-3 text-sm font-semibold tracking-wide text-slate-800 uppercase">{{ $item->title }}</a>
                    @endif
                @endforeach
                <div class="py-4">
                    <a href="{{ $site->anchor('contact') }}" @click="close()" class="flex items-center justify-center gap-2 rounded-btn bg-primary px-5 py-3 text-xs font-bold tracking-wider text-on-primary uppercase"><x-icon name="clipboard" class="size-4" /> Request Quote</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-secondary text-on-secondary/70">
        <div class="border-b border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-6 py-10 md:flex-row md:items-center">
                <div>
                    <p class="font-mono text-xs tracking-widest text-on-secondary/50 uppercase">// Kebutuhan produksi skala besar?</p>
                    <p class="mt-2 font-heading text-2xl font-bold text-on-secondary">Dapatkan penawaran teknis dalam 1x24 jam.</p>
                </div>
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-6 py-3.5 text-xs font-bold tracking-wider text-on-primary uppercase transition hover:opacity-90">Request Quote <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
        <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="text-on-secondary">
                    <x-site.logo :company="$company" text-class="text-base font-bold uppercase" img-class="h-10 w-auto brightness-0 invert" />
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 200) }}</p>
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-9 items-center justify-center border border-white/15 text-on-secondary transition hover:border-primary hover:bg-primary hover:text-on-primary" />
            </div>
            <div class="lg:col-span-2">
                <h3 class="font-mono text-xs font-semibold tracking-widest text-on-secondary uppercase">Navigasi</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach ($menu->take(7) as $item)
                        @if ($item->url)
                            <li><a {!! $item->attributes() !!} class="hover:text-primary">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="font-mono text-xs font-semibold tracking-widest text-on-secondary uppercase">Lini Produk</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @forelse ($company->products->pluck('category')->filter()->unique()->take(6) as $category)
                        <li><a href="{{ $site->anchor('products') }}" class="flex items-center gap-2 hover:text-primary"><span class="size-1.5 bg-primary"></span> {{ $category }}</a></li>
                    @empty
                        @foreach ($company->services->take(6) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="flex items-center gap-2 hover:text-primary"><span class="size-1.5 bg-primary"></span> {{ $service->title }}</a></li>
                        @endforeach
                    @endforelse
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="font-mono text-xs font-semibold tracking-widest text-on-secondary uppercase">Kantor & Pabrik</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @if ($company->fullAddress())
                        <li class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->fullAddress() }}</li>
                    @endif
                    @if ($company->phone)
                        <li class="flex gap-3"><x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-primary" /> <a href="tel:{{ $company->phone }}" class="hover:text-primary">{{ $company->phone }}</a></li>
                    @endif
                    @if ($company->email)
                        <li class="flex gap-3"><x-icon name="mail" class="mt-0.5 size-4 shrink-0 text-primary" /> <a href="mailto:{{ $company->email }}" class="break-all hover:text-primary">{{ $company->email }}</a></li>
                    @endif
                    @if ($company->working_hours)
                        <li class="flex gap-3"><x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->working_hours }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 bg-black/20">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-5 text-xs md:flex-row md:items-center md:justify-between">
                <p class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span>&copy; {{ date('Y') }} {{ $company->name }}</span>
                    <span class="flex items-center gap-1.5"><x-icon name="shield" class="size-3.5 text-primary" /> Diproduksi sesuai standar mutu & K3</span>
                </p>
                <div class="flex flex-wrap gap-4">
                    @foreach ($pages->take(4) as $p)
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
