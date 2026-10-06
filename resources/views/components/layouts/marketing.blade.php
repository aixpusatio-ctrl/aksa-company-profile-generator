@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <x-layouts.partials-head :title="$title" />
    <meta name="description" content="{{ $description ?? setting('default_seo_description') }}">
    <meta name="keywords" content="{{ setting('default_seo_keywords') }}">
    <meta property="og:title" content="{{ $title ?? setting('default_seo_title') }}">
    <meta property="og:description" content="{{ $description ?? setting('default_seo_description') }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
</head>
<body class="bg-white text-slate-600">
    <header x-data="{ open: false, scrolled: false }" x-init="addEventListener('scroll', () => scrolled = scrollY > 10)"
            class="sticky top-0 z-50 transition" :class="scrolled ? 'bg-white/90 shadow-sm backdrop-blur' : 'bg-white/0'">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}"><x-brand-logo /></a>
            <nav class="hidden items-center gap-1 lg:flex">
                @foreach (['features' => 'Features', 'templates' => 'Templates', 'pricing' => 'Pricing', 'faq' => 'FAQ'] as $anchor => $label)
                    <a href="{{ $anchor === 'templates' ? route('templates.index') : route('home').'#'.$anchor }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="hidden items-center gap-2 lg:flex">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="btn btn-primary">Dashboard <x-icon name="arrow-right" class="size-4" /></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
            <button class="btn-ghost rounded-lg p-2 lg:hidden" @click="open = !open" aria-label="Menu"><x-icon name="menu" class="size-6" /></button>
        </div>
        <div x-cloak x-show="open" x-collapse class="border-t border-slate-100 bg-white px-4 pb-6 lg:hidden">
            <nav class="flex flex-col py-3">
                @foreach (['features' => 'Features', 'templates' => 'Templates', 'pricing' => 'Pricing', 'faq' => 'FAQ'] as $anchor => $label)
                    <a href="{{ $anchor === 'templates' ? route('templates.index') : route('home').'#'.$anchor }}" @click="open = false" class="py-2.5 text-sm font-semibold text-slate-700">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="grid grid-cols-2 gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary col-span-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer class="border-t border-slate-200 bg-slate-50">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-4">
            <div class="md:col-span-2">
                <x-brand-logo />
                <p class="mt-4 max-w-sm text-sm leading-relaxed">{{ setting('default_seo_description') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Produk</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}#features" class="hover:text-slate-900">Features</a></li>
                    <li><a href="{{ route('templates.index') }}" class="hover:text-slate-900">Templates</a></li>
                    <li><a href="{{ route('home') }}#pricing" class="hover:text-slate-900">Pricing</a></li>
                    <li><a href="{{ route('home') }}#faq" class="hover:text-slate-900">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Akun</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-slate-900">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-slate-900">Register</a></li>
                    <li><a href="mailto:{{ setting('support_email') }}" class="hover:text-slate-900">{{ setting('support_email') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200 py-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ app_name() }}. Dibuat dengan Laravel.</div>
    </footer>
</body>
</html>
