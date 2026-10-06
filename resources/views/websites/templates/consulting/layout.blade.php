<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-stone-50 font-body text-stone-600 antialiased">
    @php
        $half = (int) ceil($menu->count() / 2);
        $navGroups = [$menu->take($half), $menu->slice($half)];
    @endphp

    <header x-data="siteNav" class="sticky top-0 z-40 border-b border-stone-200 bg-stone-50/90 backdrop-blur transition-all">
        <div class="mx-auto grid max-w-7xl grid-cols-[1fr_auto_1fr] items-center gap-6 px-6 transition-all" :class="scrolled ? 'py-3' : 'py-5'">
            @foreach ($navGroups as $g => $group)
                @if ($g === 1)
                    <a href="{{ $site->home() }}" class="col-start-2 row-start-1 justify-self-center text-stone-900">
                        <x-site.logo :company="$company" text-class="text-xl font-medium tracking-tight" />
                    </a>
                @endif
                <nav class="hidden items-center gap-7 lg:flex {{ $g === 0 ? 'col-start-1 justify-self-start' : 'col-start-3 justify-self-end' }} row-start-1" aria-label="Main">
                    @foreach ($group as $item)
                        @if ($item->hasChildren())
                            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <button type="button" @click="open = !open" class="flex items-center gap-1 py-2 text-[13px] tracking-wide transition {{ $item->active ? 'text-stone-900' : 'text-stone-500 hover:text-stone-900' }}">
                                    {{ $item->title }} <x-icon name="chevron-down" class="size-3" />
                                </button>
                                <div x-cloak x-show="open" x-transition.opacity class="absolute top-full left-1/2 w-56 -translate-x-1/2 pt-3">
                                    <div class="border border-stone-200 bg-white py-3 shadow-[0_20px_40px_-20px_rgba(0,0,0,0.15)]">
                                        @foreach ($item->children as $child)
                                            <a {!! $child->attributes() !!} class="block px-5 py-2 text-sm text-stone-500 transition hover:pl-6 hover:text-primary">{{ $child->title }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a {!! $item->attributes() !!} class="relative py-2 text-[13px] tracking-wide transition after:absolute after:inset-x-0 after:bottom-0 after:h-px after:origin-left after:bg-current after:transition {{ $item->active ? 'text-stone-900 after:scale-x-100' : 'text-stone-500 after:scale-x-0 hover:text-stone-900 hover:after:scale-x-100' }}">{{ $item->title }}</a>
                        @endif
                    @endforeach
                    @if ($g === 1)
                        <a href="{{ $site->anchor('contact') }}" class="rounded-btn border border-stone-900 px-4 py-2 text-[13px] tracking-wide text-stone-900 transition hover:bg-stone-900 hover:text-white">Jadwalkan Diskusi</a>
                    @endif
                </nav>
            @endforeach
            <div class="col-start-3 row-start-1 justify-self-end lg:hidden">
                <button type="button" class="inline-flex items-center gap-2 text-xs tracking-widest text-stone-700 uppercase" @click="open = !open" aria-label="Menu">
                    <span x-text="open ? 'Tutup' : 'Menu'">Menu</span>
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-t border-stone-200 bg-stone-50 lg:hidden">
            <nav class="px-6 py-6">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }" class="border-b border-stone-200">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-4 font-heading text-xl text-stone-900">
                                {{ $item->title }} <x-icon name="plus" class="size-4 transition" ::class="sub && 'rotate-45'" />
                            </button>
                            <div x-show="sub" x-collapse class="pb-4 pl-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-1.5 text-sm text-stone-500">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block border-b border-stone-200 py-4 font-heading text-xl text-stone-900">{{ $item->title }}</a>
                    @endif
                @endforeach
                <a href="{{ $site->anchor('contact') }}" @click="close()" class="mt-6 flex justify-center rounded-btn bg-stone-900 px-5 py-3.5 text-sm tracking-wide text-white">Jadwalkan Diskusi</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-stone-200 bg-stone-100/60">
        <div class="mx-auto max-w-7xl px-6 pt-20 pb-10">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Mari berbincang</p>
                    <a href="{{ $site->anchor('contact') }}" class="group mt-5 block max-w-3xl font-heading text-4xl leading-tight text-stone-900 md:text-5xl xl:text-6xl">
                        Setiap keputusan besar <em class="text-primary">dimulai</em> dengan percakapan.
                        <x-icon name="arrow-up-right" class="inline size-8 align-middle text-stone-400 transition group-hover:translate-x-1 group-hover:-translate-y-1 group-hover:text-primary md:size-10" />
                    </a>
                </div>
                <div class="grid gap-8 text-sm sm:grid-cols-2 lg:col-span-5 lg:pl-10">
                    <div>
                        <p class="text-xs tracking-[0.25em] text-stone-400 uppercase">Kantor</p>
                        <p class="mt-3 leading-relaxed text-stone-600">{{ $company->fullAddress() ?: ($company->city ?: 'Indonesia') }}</p>
                    </div>
                    <div class="space-y-1.5">
                        <p class="text-xs tracking-[0.25em] text-stone-400 uppercase">Kontak</p>
                        @if ($company->email)<a href="mailto:{{ $company->email }}" class="mt-3 block break-all text-stone-600 hover:text-primary">{{ $company->email }}</a>@endif
                        @if ($company->phone)<a href="tel:{{ $company->phone }}" class="block text-stone-600 hover:text-primary">{{ $company->phone }}</a>@endif
                    </div>
                    <div class="sm:col-span-2">
                        <x-site.social :company="$company" link-class="inline-flex size-9 items-center justify-center rounded-full border border-stone-300 text-stone-500 transition hover:border-stone-900 hover:text-stone-900" />
                    </div>
                </div>
            </div>
            <div class="mt-16 flex flex-col gap-4 border-t border-stone-200 pt-8 text-xs text-stone-400 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3 text-stone-700">
                    <x-site.logo :company="$company" text-class="text-base" img-class="h-7 w-auto" />
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-2">
                    @foreach ($menu->take(6) as $item)
                        @if ($item->url)<a {!! $item->attributes() !!} class="hover:text-stone-900">{{ $item->title }}</a>@endif
                    @endforeach
                    @foreach ($pages->take(3) as $p)
                        <a href="{{ $site->page($p->slug) }}" class="hover:text-stone-900">{{ $p->title }}</a>
                    @endforeach
                </div>
                <p>&copy; {{ date('Y') }} {{ $company->name }}</p>
            </div>
        </div>
    </footer>

    @include('websites.partials.preview-bar')
</body>
</html>
