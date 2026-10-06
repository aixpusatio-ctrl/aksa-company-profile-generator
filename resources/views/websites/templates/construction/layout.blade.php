<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-white font-body text-stone-600 antialiased">

    {{-- Top info strip --}}
    <div class="hidden border-b border-white/10 bg-stone-950 text-xs text-stone-400 md:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6">
            <div class="flex items-center divide-x divide-white/10">
                @if ($company->phone)
                    <a href="tel:{{ $company->phone }}" class="flex items-center gap-2 py-2.5 pr-5 hover:text-white"><x-icon name="phone" class="size-3.5 text-primary" /> {{ $company->phone }}</a>
                @endif
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="flex items-center gap-2 px-5 py-2.5 hover:text-white"><x-icon name="mail" class="size-3.5 text-primary" /> {{ $company->email }}</a>
                @endif
                @if ($company->working_hours)
                    <span class="flex items-center gap-2 px-5 py-2.5"><x-icon name="clock" class="size-3.5 text-primary" /> {{ $company->working_hours }}</span>
                @endif
            </div>
            <x-site.social :company="$company" class="gap-0" icon-class="size-3.5" link-class="inline-flex size-9 items-center justify-center border-l border-white/10 transition hover:bg-primary hover:text-on-primary" />
        </div>
    </div>

    {{-- Header: heavy black bar --}}
    <header x-data="siteNav" class="sticky top-0 z-40 bg-stone-950 text-white" :class="scrolled && 'shadow-2xl shadow-black/40'">
        <div class="mx-auto flex h-20 max-w-7xl items-stretch justify-between gap-6 pl-5 sm:pl-6 lg:pr-0">
            <a href="{{ $site->home() }}" class="flex items-center">
                <x-site.logo :company="$company" text-class="text-lg font-bold tracking-wide uppercase" img-class="h-11 w-auto" />
            </a>

            <nav class="hidden items-stretch lg:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative flex" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 border-b-4 px-4 pt-1 font-heading text-[15px] font-semibold tracking-wider uppercase transition {{ $item->active ? 'border-primary text-white' : 'border-transparent text-stone-300 hover:border-primary hover:text-white' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5 text-primary" />
                            </button>
                            <div x-cloak x-show="open" x-transition.origin.top class="absolute left-0 top-full w-60">
                                <div class="border-t-4 border-primary bg-white py-2 shadow-2xl">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="flex items-center gap-2 px-5 py-2.5 font-heading text-sm font-medium tracking-wide text-stone-800 uppercase transition hover:bg-stone-100 hover:pl-6 hover:text-stone-950">
                                            <span class="h-0.5 w-3 bg-primary"></span> {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="flex items-center border-b-4 px-4 pt-1 font-heading text-[15px] font-semibold tracking-wider uppercase transition {{ $item->active ? 'border-primary text-white' : 'border-transparent text-stone-300 hover:border-primary hover:text-white' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-stretch">
                <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-3 bg-primary px-8 font-heading text-sm font-bold tracking-widest text-on-primary uppercase transition hover:brightness-110 sm:flex [clip-path:polygon(18px_0,100%_0,100%_100%,0_100%)] lg:pl-10">
                    Minta Penawaran <x-icon name="arrow-right" class="size-4" />
                </a>
                <button type="button" class="inline-flex w-20 items-center justify-center bg-stone-800 text-white lg:hidden" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-6" x-show="!open" />
                    <x-icon name="x" class="size-6" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-t-4 border-primary bg-stone-900 lg:hidden">
            <nav class="divide-y divide-white/10 px-5 py-2">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-3.5 font-heading text-base font-semibold tracking-wider text-white uppercase">
                                {{ $item->title }} <x-icon name="plus" class="size-4 text-primary transition" ::class="sub && 'rotate-45'" />
                            </button>
                            <div x-show="sub" x-collapse class="mb-3 border-l-4 border-primary bg-stone-950/60">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block px-4 py-2.5 text-sm font-medium tracking-wide text-stone-300 uppercase">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block py-3.5 font-heading text-base font-semibold tracking-wider uppercase {{ $item->active ? 'text-primary' : 'text-white' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="flex items-center justify-center gap-2 bg-primary px-5 py-4 font-heading text-sm font-bold tracking-widest text-on-primary uppercase">Minta Penawaran <x-icon name="arrow-right" class="size-4" /></a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-stone-950 text-stone-400">
        {{-- Contact strip --}}
        <div class="bg-primary text-on-primary">
            <div class="mx-auto grid max-w-7xl divide-y divide-black/15 px-5 sm:px-6 md:grid-cols-3 md:divide-x md:divide-y-0">
                @foreach ([
                    ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['map-pin', 'Kantor', $company->city ?: $company->fullAddress(), $company->google_maps_url],
                ] as [$icon, $label, $value, $href])
                    @if ($value)
                        <a @if ($href) href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif @endif class="group flex items-center gap-4 py-6 md:px-8 md:first:pl-0">
                            <span class="inline-flex size-12 shrink-0 items-center justify-center bg-stone-950 text-primary"><x-icon :name="$icon" class="size-5" /></span>
                            <span class="min-w-0">
                                <span class="block font-heading text-xs font-semibold tracking-[0.2em] uppercase opacity-70">{{ $label }}</span>
                                <span class="block truncate font-heading text-lg font-bold tracking-wide">{{ $value }}</span>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 sm:px-6 md:grid-cols-2 lg:grid-cols-12 lg:py-20">
            <div class="lg:col-span-4">
                <div class="text-white"><x-site.logo :company="$company" text-class="text-xl font-bold uppercase tracking-wide" img-class="h-12 w-auto brightness-0 invert" /></div>
                <p class="mt-6 text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 190) }}</p>
                <x-site.social :company="$company" class="mt-6 gap-1" link-class="inline-flex size-10 items-center justify-center bg-stone-800 text-stone-300 transition hover:bg-primary hover:text-on-primary" />
            </div>
            <div class="lg:col-span-2">
                <h3 class="flex items-center gap-3 font-heading text-base font-bold tracking-widest text-white uppercase"><span class="h-1 w-6 bg-primary"></span> Menu</h3>
                <ul class="mt-6 space-y-3 text-sm">
                    @foreach ($menu->take(7) as $item)
                        @if ($item->url)
                            <li><a {!! $item->attributes() !!} class="transition hover:text-primary">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="flex items-center gap-3 font-heading text-base font-bold tracking-widest text-white uppercase"><span class="h-1 w-6 bg-primary"></span> Layanan</h3>
                <ul class="mt-6 space-y-3 text-sm">
                    @forelse ($company->services->take(6) as $service)
                        <li><a href="{{ $site->anchor('services') }}" class="flex items-center gap-2 transition hover:text-primary"><x-icon name="chevron-right" class="size-3.5 text-primary" /> {{ $service->title }}</a></li>
                    @empty
                        @foreach ($pages->take(6) as $p)
                            <li><a href="{{ $site->page($p->slug) }}" class="transition hover:text-primary">{{ $p->title }}</a></li>
                        @endforeach
                    @endforelse
                </ul>
            </div>
            <div class="lg:col-span-3">
                <h3 class="flex items-center gap-3 font-heading text-base font-bold tracking-widest text-white uppercase"><span class="h-1 w-6 bg-primary"></span> Kantor Pusat</h3>
                <ul class="mt-6 space-y-4 text-sm">
                    @if ($company->fullAddress())<li class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->fullAddress() }}</li>@endif
                    @if ($company->working_hours)<li class="flex gap-3"><x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->working_hours }}</li>@endif
                    @if ($company->whatsapp)<li class="flex gap-3"><x-icon name="whatsapp" class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $company->whatsapp }}</li>@endif
                </ul>
            </div>
        </div>
        <div class="h-2 bg-[repeating-linear-gradient(-45deg,var(--brand-primary)_0_12px,transparent_12px_24px)] opacity-80"></div>
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-6 text-xs tracking-wide uppercase sm:flex-row sm:px-6">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Hak cipta dilindungi.</p>
            <div class="flex flex-wrap justify-center gap-x-5 gap-y-2">
                @foreach ($pages->take(4) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-primary">{{ $p->title }}</a>
                @endforeach
            </div>
        </div>
    </footer>

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
