{{-- Global media library picker modal (listens for "open-media-picker"). --}}
<div x-data="mediaPicker(@js(route('media.index')))"
     x-on:open-media-picker.window="callback = $event.detail.pick; show()"
>
    <div x-cloak x-show="open" class="fixed inset-0 z-[70] flex items-center justify-center p-4" x-on:keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-900">Media Library</h3>
                <input type="search" x-model.debounce.400ms="search" @input.debounce.400ms="load(1)" placeholder="Cari file..." class="form-input ml-auto max-w-xs py-1.5">
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600"><x-icon name="x" class="size-5" /></button>
            </div>
            <div class="flex-1 overflow-y-auto p-5">
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    <template x-for="item in items" :key="item.id">
                        <button type="button" @click="callback && callback(item); open = false" class="group relative aspect-square overflow-hidden rounded-lg border border-slate-200 hover:ring-2 hover:ring-brand-500">
                            <img :src="item.url" :alt="item.filename" class="size-full object-cover">
                            <span class="absolute inset-x-0 bottom-0 truncate bg-black/60 px-2 py-1 text-left text-[11px] text-white" x-text="item.filename"></span>
                        </button>
                    </template>
                </div>
                <p x-show="!loading && items.length === 0" class="py-10 text-center text-sm text-slate-500">Belum ada gambar. Unggah melalui menu <a href="{{ route('media.index') }}" class="font-semibold text-brand-600">Media</a> atau langsung dari field gambar.</p>
                <div class="mt-4 text-center" x-show="page < lastPage"><button type="button" class="btn btn-secondary btn-sm" @click="load(page + 1)">Muat lagi</button></div>
            </div>
        </div>
    </div>
</div>
