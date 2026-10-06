<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('websites.partials.head')
</head>
<body class="bg-white font-body text-neutral-900 antialiased selection:bg-neutral-900 selection:text-white">

    {{-- Header: name left, inline text links right --}}
    <header x-data="siteNav" class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm" :class="scrolled && 'border-b border-neutral-200'">
        <div class="mx-auto flex max-w-4xl items-center justify-between gap-6 px-6 py-6">
            <a href="{{ $site->home() }}" class="min-w-0 text-neutral-950">
                @if ($company->logo)
                    <x-site.logo :company="$company" img-class="h-7 w-auto" />
                @else
                    <span class="font-heading text-[15px] font-semibold tracking-tight">{{ $company->name }}</span>
                @endif
            </a>

            <nav class="hidden items-center gap-7 md:flex" aria-label="Main">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" @click="open = !open" class="text-sm {{ $item->active ? 'text-neutral-950 underline underline-offset-[6px]' : 'text-neutral-500 hover:text-neutral-950' }}">
                                {{ $item->title }}<span class="ml-0.5 text-neutral-400">+</span>
                            </button>
                            <div x-cloak x-show="open" x-transition.opacity class="absolute top-full right-0 pt-3">
                                <div class="min-w-48 border border-neutral-900 bg-white py-2">
                                    @foreach ($item->children as $child)
                                        <a {!! $child->attributes() !!} class="block px-4 py-1.5 text-sm text-neutral-600 hover:text-neutral-950 hover:underline hover:underline-offset-4">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} class="text-sm {{ $item->active ? 'text-neutral-950 underline underline-offset-[6px]' : 'text-neutral-500 hover:text-neutral-950' }}">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>

            <button type="button" class="text-sm text-neutral-950 md:hidden" @click="open = !open" aria-label="Menu">
                <span x-show="!open">Menu</span>
                <span x-show="open" x-cloak>Tutup</span>
            </button>
        </div>

        {{-- Mobile navigation --}}
        <div x-cloak x-show="open" x-collapse class="border-b border-neutral-200 bg-white md:hidden">
            <nav class="mx-auto max-w-4xl px-6 pt-2 pb-8">
                @foreach ($menu as $item)
                    @if ($item->hasChildren())
                        <div x-data="{ sub: false }" class="border-t border-neutral-200">
                            <button type="button" @click="sub = !sub" class="flex w-full items-center justify-between py-4 font-heading text-2xl tracking-tight">
                                {{ $item->title }} <span class="text-neutral-400" x-text="sub ? '−' : '+'">+</span>
                            </button>
                            <div x-show="sub" x-collapse class="pb-4">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} @click="close()" class="block py-1.5 text-neutral-500">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a {!! $item->attributes() !!} @click="close()" class="block border-t border-neutral-200 py-4 font-heading text-2xl tracking-tight">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer: one line --}}
    <footer class="mx-auto max-w-4xl px-6">
        <div class="flex flex-col gap-3 border-t border-neutral-900 py-8 text-xs text-neutral-500 sm:flex-row sm:items-center sm:justify-between">
            <p class="shrink-0">&copy; {{ date('Y') }} {{ $company->name }}@if ($company->city) — {{ $company->city }}@endif</p>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 sm:justify-end">
                @foreach ($pages->take(2) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="hover:text-neutral-950">{{ $p->title }}</a>
                @endforeach
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="hover:text-neutral-950">{{ $company->email }}</a>
                @endif
                @foreach ($company->socialLinks() as $network => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="capitalize hover:text-neutral-950">{{ $network === 'x' ? 'X' : $network }}</a>
                @endforeach
            </div>
        </div>
    </footer>

    @include('websites.partials.preview-bar')
</body>
</html>
