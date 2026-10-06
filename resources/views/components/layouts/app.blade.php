@props(['title' => null])
@php
    $nav = [
        [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'websites.index', 'active' => request()->routeIs('websites.shop.*') ? ['websites.index'] : ['websites.*'], 'label' => 'My Websites', 'icon' => 'globe'],
            ['route' => 'shop.hub', 'active' => ['shop.*', 'websites.shop.*'], 'label' => 'Online Shop', 'icon' => 'shopping-bag'],
            ['route' => 'dashboard.templates', 'label' => 'Templates', 'icon' => 'template'],
            ['route' => 'domains.index', 'label' => 'Domains', 'icon' => 'link'],
            ['route' => 'media.index', 'label' => 'Media', 'icon' => 'photo'],
        ],
        'Akun' => [
            ['route' => 'notifications.index', 'label' => 'Notifikasi', 'icon' => 'bell', 'badge' => auth()->user()->unreadNotifications()->count() ?: null],
            ['route' => 'settings.edit', 'label' => 'Settings', 'icon' => 'cog'],
        ],
    ];
@endphp
<x-layouts.shell :title="$title" :nav="$nav" area="user" :search-action="route('websites.index')" search-placeholder="Cari website...">
    {{ $slot }}
</x-layouts.shell>
