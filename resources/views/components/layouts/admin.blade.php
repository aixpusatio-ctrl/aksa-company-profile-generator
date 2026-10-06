@props(['title' => null])
@php
    $nav = [
        [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ],
        'Platform' => [
            ['route' => 'admin.users.index', 'active' => ['admin.users.*'], 'label' => 'Users', 'icon' => 'users'],
            ['route' => 'admin.companies.index', 'active' => ['admin.companies.*'], 'label' => 'Company Profiles', 'icon' => 'building'],
            ['route' => 'admin.pages.index', 'label' => 'Pages', 'icon' => 'document'],
            ['route' => 'admin.domains.index', 'label' => 'Domains', 'icon' => 'globe'],
            ['route' => 'admin.subscriptions.index', 'label' => 'Subscriptions', 'icon' => 'credit-card'],
        ],
        'E-Commerce' => [
            ['route' => 'admin.shop.shops', 'label' => 'Shops', 'icon' => 'briefcase'],
            ['route' => 'admin.shop.products', 'label' => 'Products', 'icon' => 'cube'],
            ['route' => 'admin.shop.orders', 'active' => ['admin.shop.orders', 'admin.shop.orders.*'], 'label' => 'Orders', 'icon' => 'list'],
            ['route' => 'admin.shop.customers', 'label' => 'Customers', 'icon' => 'user'],
        ],
        'Templates' => [
            ['route' => 'admin.templates.index', 'active' => ['admin.templates.*'], 'label' => 'Templates', 'icon' => 'template'],
            ['route' => 'admin.categories.index', 'label' => 'Template Categories', 'icon' => 'tag'],
        ],
        'Sistem' => [
            ['route' => 'admin.media.index', 'label' => 'Media', 'icon' => 'photo'],
            ['route' => 'admin.settings.edit', 'label' => 'Settings', 'icon' => 'cog'],
            ['route' => 'admin.logs.index', 'label' => 'System Logs', 'icon' => 'clipboard'],
        ],
    ];
@endphp
<x-layouts.shell :title="$title" :nav="$nav" area="admin" :search-action="route('admin.companies.index')" search-placeholder="Cari website / perusahaan...">
    {{ $slot }}
</x-layouts.shell>
