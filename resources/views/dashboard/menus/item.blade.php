<div class="flex items-center gap-3 px-3 py-2.5">
    <button type="button" data-handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Urutkan"><x-icon name="bars" class="size-5" /></button>
    <div class="min-w-0 flex-1">
        <p class="flex items-center gap-2 text-sm font-semibold text-slate-900">
            {{ $menu->title }}
            @if ($menu->status === 'inactive')<span class="badge badge-slate">Hidden</span>@endif
        </p>
        <p class="truncate text-xs text-slate-500">
            @switch($menu->type)
                @case('anchor') <x-icon name="link" class="inline size-3" /> Section #{{ $menu->url }} @break
                @case('page') <x-icon name="document" class="inline size-3" /> Halaman: {{ $menu->page?->title ?? '(halaman dihapus)' }} @break
                @case('url') <x-icon name="external" class="inline size-3" /> {{ $menu->url }} @break
                @default <x-icon name="list" class="inline size-3" /> Grup submenu
            @endswitch
        </p>
    </div>
    <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'menu-{{ $menu->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
    <x-confirm-delete :action="route('websites.menus.destroy', [$company, $menu])" message="Hapus menu ini beserta submenunya?" />
</div>
