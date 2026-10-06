<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-white font-body text-slate-600 antialiased">

    {{-- Header: thin brand rule, logo left, nav, phone + booking button --}}
    <header x-data="siteNav" class="sticky top-0 z-40 border-t-4 border-primary bg-white" :class="scrolled ? 'shadow-md shadow-slate-900/5' : 'border-b border-slate-200'">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-6">
            <a href="{{ $site->home() }}" class="min-w-0 text-slate-900">
                <x-site.logo :company="$company" text-class="text-base font-bold sm:text-lg" />
            </a>

            <nav class="hidden items-center gap-7 xl:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-1 py-7 text-sm font-medium {{ $item->active ? 'text-primary' : 'text-slate-700 hover:text-primary' }}">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5 transition" ::class="open && 'rotate-180'" />
                            </button>
                            <div x-cloak x-show="open" x-transition.origin.top class="absolute top-full left-1/2 w-64 -translate-x-1/2">
                                <div class="rounded-brand border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="flex items-center justify-between rounded-brand px-3 py-2.5 text-sm text-slate-700 hover:bg-primary/5 hover:text-primary">
                                            {{ $child->title }} <x-icon name="chevron-right" class="size-3.5 opacity-40" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="relative py-7 text-sm font-medium after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:bg-primary after:transition {{ $item->active ? 'text-primary after:scale-x-100' : 'text-slate-700 after:scale-x-0 hover:text-primary hover:after:scale-x-100' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-5">
                @if ($company->phone)
                    <a href="tel:{{ $company->phone }}" class="hidden items-center gap-3 2xl:flex">
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-secondary/15 text-primary"><x-icon name="phone" class="size-4" /></span>
                        <span class="leading-tight">
                            <span class="block text-[11px] font-medium tracking-wide text-slate-500 uppercase">Hubungi kantor</span>
                            <span class="block text-sm font-semibold whitespace-nowrap text-slate-900">{{ $company->phone }}</span>
                        </span>
                    </a>
                @endif
                <a href="{{ $site->anchor('contact') }}" class="hidden items-center gap-2 rounded-btn bg-primary px-5 py-3 text-sm font-semibold whitespace-nowrap text-on-primary transition hover:opacity-90 sm:inline-flex">
                    <x-icon name="calendar" class="size-4" /> Jadwalkan Konsultasi
                </a>
                <button type="button" class="inline-flex size-11 items-center justify-center rounded-brand border border-slate-200 text-slate-800 xl:hidden" @click="open = !open" aria-label="Menu">
                    <x-icon name="menu" class="size-5" x-show="!open" />
                    <x-icon name="x" class="size-5" x-show="open" x-cloak />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-t border-slate-200 bg-white xl:hidden">
            <nav class="mx-auto max-w-7xl divide-y divide-slate-100 px-6 py-2">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-3.5 text-[15px] font-medium text-slate-900">
                                {{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                            </button>
                            <div x-show="sub" x-collapse class="pb-3">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="flex items-center gap-2 py-2 pl-3 text-sm text-slate-600"><span class="size-1 rounded-full bg-secondary"></span> {{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block py-3.5 text-[15px] font-medium {{ $item->active ? 'text-primary' : 'text-slate-900' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
                <div class="grid gap-3 py-5 sm:grid-cols-2">
                    <a href="{{ $site->anchor('contact') }}" @click="close()" class="flex items-center justify-center gap-2 rounded-btn bg-primary px-5 py-3 text-sm font-semibold text-on-primary"><x-icon name="calendar" class="size-4" /> Jadwalkan Konsultasi</a>
                    @if ($company->phone)
                        <a href="tel:{{ $company->phone }}" class="flex items-center justify-center gap-2 rounded-btn border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-800"><x-icon name="phone" class="size-4" /> {{ $company->phone }}</a>
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer: appointment strip + office info, hours and links --}}
    <footer class="bg-primary text-on-primary/75">
        <div class="border-b border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-6 py-10 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-full border border-secondary/60 text-secondary"><x-icon name="calendar" class="size-5" /></span>
                    <div>
                        <p class="font-heading text-xl text-on-primary">Butuh pendampingan profesional?</p>
                        <p class="text-sm">Konsultasi awal bersifat rahasia dan tanpa kewajiban.</p>
                    </div>
                </div>
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center justify-center gap-2 rounded-btn bg-secondary px-6 py-3 text-sm font-semibold text-on-secondary transition hover:opacity-90">Jadwalkan Konsultasi <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>

        <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="text-on-primary">
                    <x-site.logo :company="$company" box-class="bg-white text-primary" text-class="text-lg font-bold" img-class="h-10 w-auto brightness-0 invert" />
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($company->description, 200) }}</p>
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-9 items-center justify-center rounded-full border border-white/20 text-on-primary transition hover:border-secondary hover:text-secondary" />
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold tracking-[0.2em] text-secondary uppercase">Kantor</h3>
                <ul class="mt-5 space-y-4 text-sm">
                    @if ($company->fullAddress())
                        <li class="flex gap-3"><x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-secondary" /> <span>{{ $company->fullAddress() }}</span></li>
                    @endif
                    @if ($company->phone)
                        <li><a href="tel:{{ $company->phone }}" class="flex gap-3 hover:text-on-primary"><x-icon name="phone" class="mt-0.5 size-4 shrink-0 text-secondary" /> {{ $company->phone }}</a></li>
                    @endif
                    @if ($company->email)
                        <li><a href="mailto:{{ $company->email }}" class="flex gap-3 break-all hover:text-on-primary"><x-icon name="mail" class="mt-0.5 size-4 shrink-0 text-secondary" /> {{ $company->email }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold tracking-[0.2em] text-secondary uppercase">Jam Layanan</h3>
                <div class="mt-5 rounded-brand border border-white/15 p-5 text-sm">
                    <div class="flex items-start gap-3">
                        <x-icon name="clock" class="mt-0.5 size-4 shrink-0 text-secondary" />
                        <p class="text-on-primary">{{ $company->working_hours ?: 'Senin – Jumat, 08.00 – 17.00' }}</p>
                    </div>
                    <p class="mt-3 border-t border-white/10 pt-3 text-xs">Di luar jam kerja, silakan tinggalkan pesan — kami membalas pada hari kerja berikutnya.</p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold tracking-[0.2em] text-secondary uppercase">Tautan</h3>
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach ($menu->take(6) as $item)
                        @if ($item->url)
                            <li><a {!! $item->attributes() !!} class="hover:text-on-primary">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                    @foreach ($pages->take(4) as $p)
                        <li><a href="{{ $site->page($p->slug) }}" class="hover:text-on-primary">{{ $p->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-6 text-xs sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak dilindungi.</p>
                <p class="opacity-70">Informasi di situs ini bukan merupakan nasihat profesional resmi.</p>
            </div>
        </div>
    </footer>

    @include('websites.partials.floating-whatsapp')
    @include('websites.partials.preview-bar')
</body>
</html>
