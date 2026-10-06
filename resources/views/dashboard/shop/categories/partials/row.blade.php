<div class="flex items-center gap-3 px-4 py-3 sm:px-6 {{ $child ? 'pl-10 sm:pl-14' : '' }}">
    @if ($child)<span class="text-slate-300">└</span>@endif
    @if ($category->url('image'))
        <img src="{{ $category->url('image') }}" alt="" class="size-10 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
    @else
        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="tag" class="size-4" /></span>
    @endif
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold {{ $category->status === 'active' ? 'text-slate-900' : 'text-slate-400' }}">{{ $category->name }}</p>
        <p class="truncate text-xs text-slate-500">/{{ $category->slug }} · {{ $category->products_count }} produk @if ($category->status !== 'active') · <span class="text-amber-600">nonaktif</span> @endif</p>
    </div>
    <a href="{{ route('websites.shop.products.index', [$company, 'category' => $category->id]) }}" class="hidden rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 sm:block" title="Lihat produk"><x-icon name="cube" class="size-4" /></a>
    <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'category-edit-{{ $category->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
    <x-confirm-delete :action="route('websites.shop.categories.destroy', [$company, $category])" message="Hapus kategori ini? Produk di dalamnya menjadi tanpa kategori." />
</div>
