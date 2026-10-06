{{--
    Desktop navigation items with dropdowns. Params: $linkClass, $activeClass, $dropdownClass (panel), $childClass
--}}
@php
    $linkClass ??= 'text-sm font-medium text-ink/80 hover:text-ink';
    $activeClass ??= 'text-primary';
    $dropdownClass ??= 'min-w-56 rounded-brand border border-line bg-card p-2 shadow-xl';
    $childClass ??= 'block rounded-md px-3 py-2 text-sm text-muted hover:bg-surface-alt hover:text-ink';
@endphp
@foreach ($menu as $item)
    @if ($item->hasChildren())
        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape="open = false">
            <button type="button" @click="open = !open" :aria-expanded="open" class="inline-flex items-center gap-1 {{ $linkClass }} {{ $item->active ? $activeClass : '' }}">
                {{ $item->title }} <x-icon name="chevron-down" class="size-3.5 transition" ::class="open && 'rotate-180'" />
            </button>
            <div x-cloak x-show="open" x-transition.origin.top class="absolute top-full left-1/2 z-50 -translate-x-1/2 pt-3">
                <div class="{{ $dropdownClass }}">
                    @foreach ($item->children as $child)
                        <a {!! $child->attributes() !!} class="{{ $childClass }}">{{ $child->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <a {!! $item->attributes() !!} class="{{ $linkClass }} {{ $item->active ? $activeClass : '' }}">{{ $item->title }}</a>
    @endif
@endforeach
