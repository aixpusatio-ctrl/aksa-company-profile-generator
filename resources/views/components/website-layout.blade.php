{{-- Shell for every "manage website" page: header + editor navigation. --}}
@props(['company', 'title' => null, 'active' => null])
@php
    $groups = [
        'Website' => [
            ['websites.show', [$company], 'Overview', 'dashboard'],
            ['websites.template.edit', [$company], 'Template', 'template'],
            ['websites.sections.index', [$company], 'Sections', 'squares'],
            ['websites.menus.index', [$company], 'Menu', 'list'],
            ['websites.pages.index', [$company], 'Pages', 'document'],
        ],
        'Konten' => [
            ['websites.edit', [$company, 'info'], 'Company Info', 'building'],
            ['websites.edit', [$company, 'about'], 'About', 'document'],
            ['websites.content.index', [$company, 'services'], 'Services', 'briefcase'],
            ['websites.content.index', [$company, 'products'], 'Products', 'cube'],
            ['websites.content.index', [$company, 'projects'], 'Projects', 'folder'],
            ['websites.content.index', [$company, 'team'], 'Team', 'users'],
            ['websites.content.index', [$company, 'testimonials'], 'Testimonials', 'chat'],
            ['websites.content.index', [$company, 'gallery'], 'Gallery', 'photo'],
            ['websites.edit', [$company, 'contact'], 'Contact', 'phone'],
        ],
        'Toko Online' => [
            ['websites.shop.overview', [$company], 'Online Shop', 'shopping-bag'],
        ],
        'Pengaturan' => [
            ['websites.edit', [$company, 'branding'], 'Branding', 'paint'],
            ['websites.edit', [$company, 'seo'], 'SEO', 'search'],
            ['websites.domains.index', [$company], 'Domain', 'globe'],
            ['websites.messages.index', [$company], 'Messages', 'mail'],
        ],
    ];
    $current = url()->current();
@endphp
<x-layouts.app :title="($title ? $title.' · ' : '').$company->name">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <a href="{{ route('websites.index') }}" class="hidden rounded-lg p-2 text-slate-400 hover:bg-white hover:text-slate-700 sm:block" title="Semua website"><x-icon name="arrow-left" class="size-5" /></a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="truncate text-xl font-bold text-slate-900 sm:text-2xl">{{ $company->name }}</h1>
                    <x-status-badge :status="$company->status" />
                </div>
                <a href="{{ $company->isPublished() ? $company->publicUrl() : route('websites.preview', $company) }}" target="_blank" class="mt-0.5 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-brand-600">
                    <x-icon name="globe" class="size-3.5" /> {{ $company->primaryHost() }} <x-icon name="external" class="size-3" />
                </a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('websites.preview', $company) }}" class="btn btn-secondary"><x-icon name="eye" class="size-4" /> Preview</a>
            @if ($company->isPublished())
                <form method="POST" action="{{ route('websites.unpublish', $company) }}">@csrf<button class="btn btn-secondary">Unpublish</button></form>
                <a href="{{ $company->publicUrl() }}" target="_blank" class="btn btn-primary"><x-icon name="external" class="size-4" /> Lihat Website</a>
            @else
                <form method="POST" action="{{ route('websites.publish', $company) }}">@csrf<button class="btn btn-success"><x-icon name="rocket" class="size-4" /> Publish</button></form>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[220px_1fr]">
        <nav class="-mx-4 flex gap-1 overflow-x-auto px-4 pb-2 lg:mx-0 lg:block lg:space-y-5 lg:overflow-visible lg:px-0 lg:pb-0">
            @foreach ($groups as $group => $links)
                <div class="flex gap-1 lg:block lg:space-y-0.5">
                    <p class="hidden px-3 pb-1 text-[11px] font-semibold tracking-wider text-slate-400 uppercase lg:block">{{ $group }}</p>
                    @foreach ($links as [$route, $params, $label, $icon])
                        @php($href = route($route, $params))
                        @php($isActive = $current === $href || ($route === 'websites.pages.index' && request()->routeIs('websites.pages.*')) || ($route === 'websites.shop.overview' && request()->routeIs('websites.shop.*')))
                        <a href="{{ $href }}" class="flex shrink-0 items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium whitespace-nowrap transition {{ $isActive ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/70 hover:text-slate-900' }}">
                            <x-icon :name="$icon" class="size-4 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" /> {{ $label }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
        <div class="min-w-0">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
