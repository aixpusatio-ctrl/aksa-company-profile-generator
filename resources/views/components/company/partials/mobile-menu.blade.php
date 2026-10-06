{{--
    Mobile navigation list (accordion for sub menus). Used inside each navbar's
    mobile panel. Expects to live inside x-data="siteNav" (uses close()).
    Params: $itemClass, $childClass
--}}
@php
    $itemClass ??= 'flex w-full items-center justify-between py-3 text-base font-semibold text-ink';
    $childClass ??= 'block py-2 text-sm text-muted';
@endphp
<nav class="divide-y divide-line" aria-label="Mobile">
    @foreach ($menu as $item)
        @if ($item->hasChildren())
            <div x-data="{ sub: false }">
                <button type="button" @click="sub = !sub" class="{{ $itemClass }}">
                    {{ $item->title }} <x-icon name="chevron-down" class="size-4 transition" ::class="sub && 'rotate-180'" />
                </button>
                <div x-show="sub" x-collapse class="pb-3 pl-4">
                    @foreach ($item->children as $child)
                        <a {!! $child->attributes() !!} @click="close()" class="{{ $childClass }}">{{ $child->title }}</a>
                    @endforeach
                </div>
            </div>
        @else
            <a {!! $item->attributes() !!} @click="close()" class="{{ $itemClass }}">{{ $item->title }}</a>
        @endif
    @endforeach
</nav>
