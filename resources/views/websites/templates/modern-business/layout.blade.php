<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
@php
    // The navbar floats transparently over the gradient hero (or the page header).
    // If the first home section is not the hero, fall back to a solid bar.
    $overHero = $page !== null || optional($sections->first())->key === 'hero';
@endphp
<body class="bg-white font-body text-slate-600 antialiased">

    {{-- Header: transparent over the hero, turns into a frosted white bar on scroll --}}
    <header x-data="siteNav" class="group fixed inset-x-0 top-0 z-40 transition-all duration-300"
            :data-solid="scrolled || open || {{ $overHero ? 'false' : 'true' }}"
            :class="(scrolled || open || {{ $overHero ? 'false' : 'true' }}) ? 'bg-white/90 shadow-lg shadow-slate-900/5 backdrop-blur-xl' : 'bg-transparent'">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-5 sm:px-6">
            <a href="{{ $site->home() }}" class="text-on-primary transition group-data-solid:text-slate-900">
                <x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" />
            </a>

            <nav class="hidden items-center gap-1 rounded-full p-1 transition lg:flex bg-white/10 ring-1 ring-white/15 backdrop-blur group-data-solid:bg-slate-100/80 group-data-solid:ring-slate-200/60" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium transition text-on-primary/85 hover:bg-white/15 hover:text-on-primary group-data-solid:text-slate-600 group-data-solid:hover:bg-white group-data-solid:hover:text-slate-900">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5" />
                            </button>
                            <div x-cloak x-show="open" x-transition.origin.top class="absolute left-1/2 top-full w-60 -translate-x-1/2 pt-3">
                                <div class="rounded-2xl border border-slate-100 bg-white p-2 shadow-2xl shadow-slate-900/10">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-primary/10 hover:text-primary">
                                            {{ $child->title }} <x-icon name="arrow-right" class="size-3.5 opacity-50" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="rounded-full px-4 py-2 text-sm font-medium transition {{ $item->active ? 'bg-white text-slate-900 shadow-sm' : 'text-on-primary/85 hover:bg-white/15 hover:text-on-primary group-data-solid:text-slate-600 group-data-solid:hover:bg-white group-data-solid:hover:text-slate-900' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-2 rounded-btn px-5 py-2.5 text-sm font-semibold shadow-lg transition sm:inline-flex bg-white text-slate-900 shadow-black/10 hover:-translate-y-0.5 group-data-solid:bg-primary group-data-solid:text-on-primary group-data-solid:shadow-primary/30">
                    Mulai Sekarang <x-icon name="arrow-up-right" class="size-4" />
                </a>
                <button type="button" class="inline-flex size-11 items-center justify-center rounded-full transition lg:hidden text-on-primary bg-white/15 group-data-solid:bg-slate-100 group-data-solid:text-slate-900" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="lg:hidden">
            <nav class="mx-4 mb-4 space-y-1 rounded-3xl border border-slate-100 bg-white p-4 shadow-xl shadow-slate-900/10">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                            </button>
                            <div x-show="sub" x-collapse class="space-y-1 pb-2 pl-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block rounded-xl px-4 py-2 text-sm text-slate-600 hover:bg-primary/10 hover:text-primary">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ $item->active ? 'bg-primary/10 text-primary' : 'text-slate-800 hover:bg-slate-50' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
                <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-2 flex items-center justify-center gap-2 rounded-btn bg-linear-to-r from-primary to-secondary px-5 py-3.5 text-sm font-semibold text-on-primary">Mulai Sekarang <x-icon name="arrow-up-right" class="size-4" /></a>
            </nav>
        </div>
    </header>

    <main class="{{ $overHero ? '' : 'pt-20' }}">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="relative mt-40 bg-slate-950 lg:mt-28 text-slate-400">
        {{-- Newsletter-style CTA strip overlapping the footer --}}
        <div class="relative z-10 mx-auto max-w-7xl -translate-y-1/2 px-5 sm:px-6">
            <div class="relative overflow-hidden rounded-brand bg-linear-to-r from-primary to-secondary p-6 shadow-2xl shadow-primary/25 sm:p-8 lg:p-10">
                <div class="absolute -top-16 -right-10 size-56 rounded-full bg-white/15 blur-2xl"></div>
                <div class="relative flex flex-col items-start gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="font-heading text-2xl font-bold text-on-primary sm:text-3xl">Punya proyek yang ingin dibahas?</h2>
                        <p class="mt-2 text-sm text-on-primary/80">Tinggalkan pesan, tim kami akan menghubungi Anda dalam 1x24 jam kerja.</p>
                    </div>
                    <div class="flex w-full items-center gap-2 rounded-full bg-white p-1.5 pl-5 shadow-lg lg:w-auto lg:min-w-[28rem]">
                        <x-icon name="mail" class="size-5 shrink-0 text-slate-400" />
                        <span class="min-w-0 flex-1 truncate text-sm text-slate-500">{{ $company->email ?: 'Kirim pesan kepada kami' }}</span>
                        <a href="{{ $company->email ? 'mailto:'.$company->email : $site->anchor('contact') }}" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Kirim <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(ellipse_at_top,color-mix(in_oklab,var(--brand-primary)_25%,transparent),transparent_70%)]"></div>

        <div class="relative mx-auto -mt-6 grid max-w-7xl gap-12 px-5 pb-14 sm:px-6 md:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="text-white">
                    <x-site.logo :company="$company" text-class="text-xl font-bold" img-class="h-10 w-auto brightness-0 invert" />
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 170) }}</p>
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-10 items-center justify-center rounded-full bg-white/5 text-slate-300 ring-1 ring-white/10 transition hover:bg-primary hover:text-on-primary hover:ring-primary" />
            </div>
            <div class="lg:col-span-2">
                <h3 class="text-sm font-semibold text-white">Navigasi</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($menu->take(7) as $item)
                        @if ($item->url)
                            <li><a {!! $item->attributes() !!} class="transition hover:text-white">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="text-sm font-semibold text-white">Layanan</h3>
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
                <h3 class="text-sm font-semibold text-white">Kantor</h3>
                <ul class="mt-5 space-y-4 text-sm">
                    @if ($company->fullAddress())
                        <li class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->fullAddress() }}</li>
                    @endif
                    @if ($company->phone)
                        <li><a href="tel:{{ $company->phone }}" class="flex gap-3 hover:text-white"><x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->phone }}</a></li>
                    @endif
                    @if ($company->working_hours)
                        <li class="flex gap-3"><x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->working_hours }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-6 text-xs sm:flex-row sm:px-6">
                <p>&copy; {{ date('Y') }} {{ $company->name }}. Hak cipta dilindungi.</p>
                <div class="flex flex-wrap justify-center gap-x-5 gap-y-2">
                    @foreach ($pages->take(4) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="transition hover:text-white">{{ $p->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
