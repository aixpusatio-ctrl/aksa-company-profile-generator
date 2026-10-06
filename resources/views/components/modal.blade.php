{{-- Alpine modal. Open with: $dispatch('open-modal', 'name') --}}
@props(['name', 'title' => null, 'maxWidth' => 'max-w-lg'])
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === @js($name)) open = true" x-on:close-modal.window="open = false" x-on:keydown.escape.window="open = false">
    <div x-cloak x-show="open" class="fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition class="relative w-full {{ $maxWidth }} overflow-hidden rounded-2xl bg-white shadow-2xl">
            @if ($title)
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600" aria-label="Tutup"><x-icon name="x" class="size-5" /></button>
                </div>
            @endif
            <div class="max-h-[80vh] overflow-y-auto p-6">{{ $slot }}</div>
        </div>
    </div>
</div>
