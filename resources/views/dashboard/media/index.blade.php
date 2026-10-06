<x-layouts.app title="Media Library">
    <x-page-header title="Media Library" :description="'Total penyimpanan: '.\Illuminate\Support\Number::fileSize((int) $usage, precision: 1)">
        <x-slot:actions>
            <button class="btn btn-primary" x-data @click="$dispatch('open-modal', 'upload')"><x-icon name="upload" class="size-4" /> Upload</button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('media.index', ['q' => $search]) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ! $collection ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">Semua</a>
        @foreach ($collections as $item)
            <a href="{{ route('media.index', ['collection' => $item, 'q' => $search]) }}" class="rounded-full px-4 py-2 text-sm font-semibold capitalize {{ $collection === $item ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">{{ $item }}</a>
        @endforeach
        <form method="GET" class="ml-auto">
            <input type="hidden" name="collection" value="{{ $collection }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari file..." class="form-input w-56">
        </form>
    </div>

    @if ($items->isEmpty())
        <x-empty-state title="Belum ada file" description="Unggah logo, foto, gambar galeri, atau dokumen PDF." icon="photo">
            <button class="btn btn-primary" x-data @click="$dispatch('open-modal', 'upload')">Upload file</button>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($items as $item)
                <div class="card group overflow-hidden" x-data="{ copied: false }">
                    <div class="relative aspect-square bg-slate-100">
                        @if ($item->is_image)
                            <img src="{{ $item->url }}" alt="{{ $item->filename }}" loading="lazy" class="size-full object-cover">
                        @else
                            <div class="flex size-full flex-col items-center justify-center text-slate-400"><x-icon name="document" class="size-10" /><span class="mt-1 text-xs font-semibold uppercase">{{ pathinfo($item->path, PATHINFO_EXTENSION) }}</span></div>
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center gap-2 bg-slate-900/50 opacity-0 transition group-hover:opacity-100">
                            <button type="button" class="btn btn-secondary btn-sm" @click="navigator.clipboard.writeText(@js($item->url)); copied = true; setTimeout(() => copied = false, 1500)"><span x-text="copied ? 'Copied!' : 'Copy URL'"></span></button>
                            <form method="POST" action="{{ route('media.destroy', $item) }}" onsubmit="return confirm('Hapus file ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><x-icon name="trash" class="size-3.5" /></button></form>
                        </div>
                    </div>
                    <div class="p-3">
                        <p class="truncate text-xs font-semibold text-slate-800" title="{{ $item->filename }}">{{ $item->filename }}</p>
                        <p class="text-[11px] text-slate-500">{{ $item->human_size }} @if ($item->width) · {{ $item->width }}×{{ $item->height }} @endif · <span class="capitalize">{{ $item->collection }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    @endif

    <x-modal name="upload" title="Upload file">
        <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-form.select name="collection" label="Koleksi" :options="array_combine($collections, array_map('ucfirst', $collections))" :value="$collection ?: 'images'" />
            <div>
                <label class="form-label">File (maks. 10 sekaligus)</label>
                <input type="file" name="files[]" multiple required class="block w-full rounded-lg border border-dashed border-slate-300 p-4 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700">
                <p class="form-help">Gambar: JPG, PNG, WEBP, GIF (maks. {{ round(setting('max_upload_kb', 4096) / 1024, 1) }} MB, maks. {{ config('platform.media.max_image_dimension') }}px). Dokumen: PDF, DOC, XLS, PPT (maks. {{ config('platform.media.max_document_kb') / 1024 }} MB).</p>
            </div>
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Upload</button></div>
        </form>
    </x-modal>
</x-layouts.app>
