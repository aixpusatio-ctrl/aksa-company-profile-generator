<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-white font-body text-slate-600 antialiased">

    {{-- Top bar --}}
    <div class="hidden bg-secondary text-on-secondary/80 text-xs md:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-2.5">
            <div class="flex items-center gap-6">
                @if ($company->phone)
                    <a href="tel:{{ $company->phone }}" class="flex items-center gap-1.5 hover:text-on-secondary"><x-icon name="phone" class="size-3.5" /> {{ $company->phone }}</a>
                @endif
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="flex items-center gap-1.5 hover:text-on-secondary"><x-icon name="mail" class="size-3.5" /> {{ $company->email }}</a>
                @endif
                @if ($company->working_hours)
                    <span class="flex items-center gap-1.5"><x-icon name="clock" class="size-3.5" /> {{ $company->working_hours }}</span>
                @endif
            </div>
            <x-site.social :company="$company" icon-class="size-3.5" link-class="inline-flex size-6 items-center justify-center rounded hover:text-on-secondary" />
        </div>
    </div>

    {{-- Header --}}
    <header x-data="siteNav" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur" :class="scrolled && 'shadow-sm'">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-6 py-3">
            <a href="{{ $site->home() }}" class="text-slate-900">
                <x-site.logo :company="$company" text-class="text-lg font-extrabold tracking-tight" />
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1 rounded-md px-3 py-2 text-sm font-semibold {{ $item->active ? 'text-primary' : 'text-slate-700 hover:text-primary' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5" />
                            </button>
                            <div x-cloak x-show="open" x-transition.origin.top class="absolute left-0 top-full w-56 pt-2">
                                <div class="overflow-hidden rounded-brand border border-slate-100 bg-white py-2 shadow-xl shadow-slate-900/10">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="block border-l-2 border-transparent px-4 py-2 text-sm text-slate-600 hover:border-primary hover:bg-slate-50 hover:text-primary">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="rounded-md px-3 py-2 text-sm font-semibold {{ $item->active ? 'text-primary' : 'text-slate-700 hover:text-primary' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ $site->anchor('contact') }}" class="hidden rounded-btn bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary shadow-sm shadow-primary/30 transition hover:opacity-90 sm:inline-flex">Hubungi Kami</a>
                <button type="button" class="inline-flex size-10 items-center justify-center rounded-md text-slate-700 lg:hidden" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-6" x-show="!open" />
                    <x-icon name="x" class="size-6" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-t border-slate-100 bg-white lg:hidden">
            <nav class="space-y-1 px-6 py-4">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-2 text-sm font-semibold text-slate-800">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                            </button>
                            <div x-show="sub" x-collapse class="ml-3 border-l border-slate-200 pl-3">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-1.5 text-sm text-slate-600">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block py-2 text-sm font-semibold text-slate-800">{{ $item->title }}</a>
                    @endif
                @endforeach
                <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-3 flex justify-center rounded-btn bg-primary px-5 py-3 text-sm font-semibold text-on-primary">Hubungi Kami</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-secondary text-on-secondary/70">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <div class="text-on-secondary">
                    <x-site.logo :company="$company" text-class="text-lg font-extrabold" img-class="h-10 w-auto brightness-0 invert" />
                </div>
                <p class="mt-5 text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 180) }}</p>
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-9 items-center justify-center rounded-full bg-white/10 text-on-secondary transition hover:bg-primary hover:text-on-primary" />
            </div>
            <div>
                <h3 class="font-heading text-sm font-bold tracking-wider text-on-secondary uppercase">Navigasi</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach ($menu->take(7) as $item)
                        @if ($item->url)
                            <li><a {!! $item->attributes() !!} class="hover:text-on-secondary">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="font-heading text-sm font-bold tracking-wider text-on-secondary uppercase">Layanan</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @forelse ($company->services->take(6) as $service)
                        <li><a href="{{ $site->anchor('services') }}" class="hover:text-on-secondary">{{ $service->title }}</a></li>
                    @empty
                        @foreach ($pages->take(6) as $p)
                            <li><a href="{{ $site->page($p->slug) }}" class="hover:text-on-secondary">{{ $p->title }}</a></li>
                        @endforeach
                    @endforelse
                </ul>
            </div>
            <div>
                <h3 class="font-heading text-sm font-bold tracking-wider text-on-secondary uppercase">Kontak</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @if ($company->fullAddress())
                        <li class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->fullAddress() }}</li>
                    @endif
                    @if ($company->phone)
                        <li class="flex gap-3"><x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->phone }}</li>
                    @endif
                    @if ($company->email)
                        <li class="flex gap-3"><x-icon name="mail" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->email }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-6 text-xs sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ $company->name }}. All rights reserved.</p>
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
