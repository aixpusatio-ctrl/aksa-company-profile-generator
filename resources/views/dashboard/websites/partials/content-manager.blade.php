{{-- List + add/edit/delete/reorder UI for one content type (services, products, ...). --}}
@php($titleField = $type['title_field'])
<div class="card">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">{{ $type['plural'] }} <span class="ml-1 text-sm font-normal text-slate-400">({{ $items->count() }})</span></h2>
            <p class="text-xs text-slate-500">{{ $type['description'] }} Seret <x-icon name="bars" class="inline size-3.5" /> untuk mengubah urutan.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" @click="$dispatch('open-modal', 'add-{{ $type['key'] }}')"><x-icon name="plus" class="size-4" /> Tambah {{ $type['singular'] }}</button>
    </div>

    @if ($items->isEmpty())
        <div class="p-6">
            <x-empty-state :title="'Belum ada '.strtolower($type['plural'])" description="Section ini otomatis tersembunyi di website sampai Anda menambahkan item." icon="plus">
                <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'add-{{ $type['key'] }}')">Tambah {{ $type['singular'] }}</button>
            </x-empty-state>
        </div>
    @else
        <div x-data="sortableList(@js(route('websites.content.reorder', [$company, $type['key']])))">
            <p x-cloak x-show="saving" class="px-6 pt-3 text-xs text-slate-500">Menyimpan urutan…</p>
            <ul x-ref="list" class="divide-y divide-slate-100">
                @foreach ($items as $item)
                    <li data-id="{{ $item->id }}" class="flex items-center gap-4 bg-white px-4 py-3 sm:px-6">
                        <button type="button" data-handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Urutkan"><x-icon name="bars" class="size-5" /></button>
                        @if ($type['image_field'] && $item->url($type['image_field']))
                            <img src="{{ $item->url($type['image_field']) }}" alt="" class="size-12 shrink-0 rounded-lg object-cover">
                        @elseif ($type['key'] === 'services')
                            <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><x-icon :name="$item->icon ?: 'briefcase'" class="size-6" /></span>
                        @else
                            <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="photo" class="size-5" /></span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $item->{$titleField} ?: '(tanpa judul)' }}</p>
                            <p class="truncate text-xs text-slate-500">{{ \Illuminate\Support\Str::limit((string) $item->{$type['subtitle_field']}, 90) }}</p>
                        </div>
                        <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'edit-{{ $type['key'] }}-{{ $item->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                        <x-confirm-delete :action="route('websites.content.destroy', [$company, $type['key'], $item->id])" />
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Modals --}}
<x-modal :name="'add-'.$type['key']" :title="'Tambah '.$type['singular']" max-width="max-w-2xl">
    <form method="POST" action="{{ route('websites.content.store', [$company, $type['key']]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('dashboard.websites.partials.content-fields', ['item' => null])
        <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
    </form>
</x-modal>
@foreach ($items as $item)
    <x-modal :name="'edit-'.$type['key'].'-'.$item->id" :title="'Edit '.$type['singular']" max-width="max-w-2xl">
        <form method="POST" action="{{ route('websites.content.update', [$company, $type['key'], $item->id]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('dashboard.websites.partials.content-fields', ['item' => $item])
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
@endforeach
