<x-layouts.admin title="Media">
    <x-page-header title="Media" description="Semua file yang diunggah pengguna di seluruh platform.">
        <x-slot:actions>
            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm">
                <x-icon name="server" class="size-4 text-slate-400" />
                <span class="text-slate-500">Total penyimpanan</span>
                <span class="font-semibold text-slate-900">{{ \Illuminate\Support\Number::fileSize((int) $totalSize, precision: 1) }}</span>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex flex-col gap-3 border-b border-slate-200 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex gap-1 overflow-x-auto">
            <a href="{{ route('admin.media.index', array_filter(['q' => $search])) }}"
               class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $collection === '' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua</a>
            @foreach ($collections as $c)
                <a href="{{ route('admin.media.index', array_filter(['collection' => $c, 'q' => $search])) }}"
                   class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $collection === $c ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">{{ ucfirst($c) }}</a>
            @endforeach
        </div>
        <form method="GET" class="mb-3 flex gap-2">
            @if ($collection)<input type="hidden" name="collection" value="{{ $collection }}">@endif
            <div class="relative flex-1 sm:w-64">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama file..." class="form-input pl-9">
            </div>
            <button class="btn btn-dark">Cari</button>
        </form>
    </div>

    @if ($items->isEmpty())
        <x-empty-state icon="photo" title="Tidak ada file" description="Belum ada file yang cocok dengan filter Anda." />
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            @foreach ($items as $item)
                <div class="card group overflow-hidden">
                    <a href="{{ $item->url }}" target="_blank" rel="noopener" class="relative block aspect-square bg-slate-100">
                        @if ($item->is_image)
                            <img src="{{ $item->url }}" alt="{{ $item->alt ?: $item->filename }}" loading="lazy" class="absolute inset-0 size-full object-cover">
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-400">
                                <x-icon name="document" class="size-10" />
                                <span class="text-xs font-semibold uppercase">{{ pathinfo($item->filename, PATHINFO_EXTENSION) ?: 'file' }}</span>
                            </div>
                        @endif
                        <span class="absolute top-2 left-2 badge badge-slate bg-white/90">{{ $item->collection }}</span>
                    </a>
                    <div class="flex items-start gap-1 p-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-medium text-slate-800" title="{{ $item->filename }}">{{ $item->filename }}</p>
                            <p class="truncate text-[11px] text-slate-500">{{ $item->human_size }}@if ($item->width) · {{ $item->width }}×{{ $item->height }}@endif</p>
                            <p class="truncate text-[11px] text-slate-400">
                                @if ($item->user)
                                    <a href="{{ route('admin.users.show', $item->user) }}" class="hover:text-brand-600">{{ $item->user->name }}</a>
                                @else — @endif
                                · {{ $item->created_at?->format('d/m/Y') }}
                            </p>
                        </div>
                        <x-confirm-delete :action="route('admin.media.destroy', $item)" message="Hapus file ini? Gambar yang memakai file ini akan rusak." class="-mt-1 -mr-1" />
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $items->links() }}</div>
    @endif
</x-layouts.admin>
