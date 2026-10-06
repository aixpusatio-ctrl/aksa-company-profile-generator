{{--
    Shared application shell: sidebar + topbar (search, notifications, profile).
    Used by <x-layouts.app> (user dashboard) and <x-layouts.admin>.
--}}
@props(['title' => null, 'nav' => [], 'area' => 'user', 'searchAction' => null, 'searchPlaceholder' => 'Cari...'])
@php
    $user = auth()->user();
    $notifications = $user->unreadNotifications()->latest()->take(6)->get();
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <x-layouts.partials-head :title="$title" />
</head>
<body class="h-full" x-data="{ sidebar: false }">
<div class="min-h-full">
    {{-- Mobile sidebar backdrop --}}
    <div x-cloak x-show="sidebar" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" @click="sidebar = false"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform lg:translate-x-0"
           :class="sidebar && 'translate-x-0'">
        <div class="flex h-16 shrink-0 items-center justify-between px-6">
            <a href="{{ $area === 'admin' ? route('admin.dashboard') : route('dashboard') }}"><x-brand-logo /></a>
            <button class="btn-ghost rounded-lg p-1.5 lg:hidden" @click="sidebar = false" aria-label="Tutup menu"><x-icon name="x" class="size-5" /></button>
        </div>

        @if ($area === 'admin')
            <div class="mx-4 mb-2 rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Admin Panel</div>
        @endif

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-4">
            @foreach ($nav as $group => $links)
                <div>
                    @if (! is_int($group))
                        <p class="mb-2 px-3 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ $group }}</p>
                    @endif
                    <ul class="space-y-1">
                        @foreach ($links as $link)
                            @php($active = request()->routeIs(...(array) ($link['active'] ?? $link['route'])))
                            <li>
                                <a href="{{ route($link['route']) }}" class="nav-link {{ $active ? 'nav-link-active' : '' }}">
                                    <x-icon :name="$link['icon']" class="size-5 {{ $active ? 'text-brand-600' : 'text-slate-400' }}" />
                                    <span class="flex-1">{{ $link['label'] }}</span>
                                    @if (! empty($link['badge']))
                                        <span class="rounded-full bg-brand-100 px-2 py-0.5 text-[11px] font-semibold text-brand-700">{{ $link['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            @if ($area === 'user')
                <div class="rounded-xl bg-gradient-to-br from-brand-600 to-violet-600 p-4 text-white">
                    <p class="text-xs font-medium text-white/70">Paket saat ini</p>
                    <p class="font-display text-lg font-bold">{{ $user->plan()['name'] }}</p>
                    @if ($user->subscription?->status === 'trialing' && $user->subscription->trial_ends_at)
                        <p class="mt-1 text-xs text-white/80">Trial berakhir {{ $user->subscription->trial_ends_at->diffForHumans() }}</p>
                    @endif
                    <a href="{{ route('settings.edit') }}#plan" class="mt-3 inline-flex text-xs font-semibold underline underline-offset-2">Kelola paket</a>
                </div>
            @else
                <a href="{{ route('dashboard') }}" class="nav-link"><x-icon name="arrow-left" class="size-5 text-slate-400" /> Ke User Dashboard</a>
            @endif
        </div>
    </aside>

    <div class="lg:pl-72">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button class="btn-ghost -ml-1 rounded-lg p-2 lg:hidden" @click="sidebar = true" aria-label="Buka menu"><x-icon name="menu" class="size-6" /></button>

            <form action="{{ $searchAction }}" method="GET" class="relative hidden max-w-md flex-1 sm:block">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $searchPlaceholder }}" class="w-full rounded-lg border-0 bg-slate-100 py-2 pr-3 pl-9 text-sm placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </form>

            <div class="ml-auto flex items-center gap-2">
                @if ($area === 'user')
                    <a href="{{ route('websites.create') }}" class="btn btn-primary btn-sm hidden md:inline-flex"><x-icon name="plus" class="size-4" /> Website Baru</a>
                @endif

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700" aria-label="Notifikasi">
                        <x-icon name="bell" class="size-6" />
                        @if ($notifications->isNotEmpty())
                            <span class="absolute top-1.5 right-1.5 size-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        @endif
                    </button>
                    <div x-cloak x-show="open" x-transition class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">Notifikasi</p>
                            @if ($notifications->isNotEmpty())
                                <form method="POST" action="{{ route('notifications.read') }}">@csrf<button class="text-xs font-medium text-brand-600 hover:underline">Tandai dibaca</button></form>
                            @endif
                        </div>
                        <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                            @forelse ($notifications as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                                    <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"><x-icon :name="$notification->data['icon'] ?? 'bell'" class="size-4" /></span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-slate-900">{{ $notification->data['title'] ?? '' }}</span>
                                        <span class="block truncate text-xs text-slate-500">{{ $notification->data['message'] ?? '' }}</span>
                                        <span class="block text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </span>
                                </a>
                            @empty
                                <p class="px-4 py-8 text-center text-sm text-slate-500">Tidak ada notifikasi baru.</p>
                            @endforelse
                        </div>
                        <a href="{{ route('notifications.index') }}" class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 hover:bg-slate-50">Lihat semua</a>
                    </div>
                </div>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-100">
                        @if ($user->avatar)
                            <img src="{{ \App\Support\MediaUrl::resolve($user->avatar) }}" alt="" class="size-8 rounded-full object-cover">
                        @else
                            <span class="inline-flex size-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-violet-500 text-xs font-bold text-white">{{ $user->initials() }}</span>
                        @endif
                        <span class="hidden text-left md:block">
                            <span class="block text-sm leading-tight font-semibold text-slate-900">{{ $user->name }}</span>
                            <span class="block text-xs leading-tight text-slate-500">{{ $user->isAdmin() ? 'Administrator' : $user->plan()['name'] }}</span>
                        </span>
                        <x-icon name="chevron-down" class="hidden size-4 text-slate-400 md:block" />
                    </button>
                    <div x-cloak x-show="open" x-transition class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="cog" class="size-4 text-slate-400" /> Pengaturan</a>
                        @if ($user->isAdmin())
                            @if ($area === 'admin')
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="dashboard" class="size-4 text-slate-400" /> User Dashboard</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="shield" class="size-4 text-slate-400" /> Admin Panel</a>
                            @endif
                        @endif
                        <a href="{{ route('home') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="home" class="size-4 text-slate-400" /> Halaman Utama</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button class="flex w-full items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50"><x-icon name="logout" class="size-4" /> Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <x-flash />
            {{ $slot }}
        </main>
    </div>
</div>
<x-media-picker />
@stack('scripts')
</body>
</html>
