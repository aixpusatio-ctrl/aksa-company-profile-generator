@props(['title' => null])
@php
    $nav = [
        [
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'websites.index', 'active' => ['websites.*'], 'label' => 'My Websites', 'icon' => 'globe'],
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
