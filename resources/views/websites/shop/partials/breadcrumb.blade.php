{{-- Breadcrumb. Vars: $crumbs ([label => url|null]); "Beranda" and "Shop" are prepended. --}}
<nav class="flex flex-wrap items-center gap-1.5 text-xs text-muted sm:text-sm" aria-label="Breadcrumb">
    <a href="{{ $site->home() }}" class="hover:text-ink">Beranda</a>
    <x-icon name="chevron-right" class="size-3" />
    <a href="{{ $site->shop() }}" class="hover:text-ink">Shop</a>
    @foreach ($crumbs as $label => $link)
        <x-icon name="chevron-right" class="size-3" />
        @if ($link)
            <a href="{{ $link }}" class="hover:text-ink">{{ $label }}</a>
        @else
            <span class="max-w-[16rem] truncate text-ink" aria-current="page">{{ $label }}</span>
        @endif
    @endforeach
</nav>
