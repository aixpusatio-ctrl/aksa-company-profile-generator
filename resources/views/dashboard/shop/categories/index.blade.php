@php
    $tagColors = [
        'slate' => 'bg-slate-100 text-slate-700', 'sky' => 'bg-sky-100 text-sky-700', 'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700', 'violet' => 'bg-violet-100 text-violet-700', 'emerald' => 'bg-emerald-100 text-emerald-700',
    ];
    $allCategories = $tree->flatMap(fn ($root) => collect([$root])->merge($root->children));
@endphp
<x-website-layout :company="$company" title="Kategori & Tag">
    @include('dashboard.shop.partials.nav')

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card min-w-0 xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Kategori produk</h2>
                    <p class="text-xs text-slate-500">Dua level: kategori utama dan sub kategori.</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" @click="$dispatch('open-modal', 'category-create')"><x-icon name="plus" class="size-4" /> Tambah kategori</button>
            </div>
            @if ($tree->isEmpty())
                <div class="p-6">
                    <x-empty-state title="Belum ada kategori" description="Kelompokkan produk agar pelanggan mudah menemukan yang mereka cari." icon="tag">
                        <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'category-create')">Tambah kategori</button>
                    </x-empty-state>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($tree as $root)
                        <li>
                            @include('dashboard.shop.categories.partials.row', ['category' => $root, 'child' => false])
                            @if ($root->children->isNotEmpty())
                                <ul class="border-t border-slate-100 bg-slate-50/50">
                                    @foreach ($root->children as $child)
                                        <li class="border-b border-slate-100 last:border-b-0">@include('dashboard.shop.categories.partials.row', ['category' => $child, 'child' => true])</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card self-start">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Tag produk</h2>
                <p class="text-xs text-slate-500">Label seperti New, Sale, Best Seller.</p>
            </div>
            <div class="card-body space-y-4">
                <div class="flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <span class="inline-flex items-center gap-1 rounded-full py-1 pr-1 pl-3 text-xs font-semibold {{ $tagColors[$tag->color] ?? $tagColors['slate'] }}">
                            {{ $tag->name }} <span class="opacity-60">({{ $tag->products_count }})</span>
                            <form method="POST" action="{{ route('websites.shop.tags.destroy', [$company, $tag]) }}" onsubmit="return confirm(@js('Hapus tag '.$tag->name.'?'))">
                                @csrf @method('DELETE')
                                <button class="rounded-full p-0.5 hover:bg-black/10" title="Hapus tag"><x-icon name="x" class="size-3" /></button>
                            </form>
                        </span>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada tag.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('websites.shop.tags.store', $company) }}" class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                    @csrf
                    <input type="text" name="name" required maxlength="60" placeholder="Nama tag" class="form-input min-w-0 flex-1">
                    <select name="color" class="form-input w-28">
                        @foreach (\App\Models\Shop\ProductTag::COLORS as $color)<option value="{{ $color }}">{{ ucfirst($color) }}</option>@endforeach
                    </select>
                    <button class="btn btn-secondary"><x-icon name="plus" class="size-4" /> Tambah</button>
                </form>
            </div>
        </div>
    </div>

    <x-modal name="category-create" title="Tambah kategori" max-width="max-w-2xl">
        <form method="POST" action="{{ route('websites.shop.categories.store', $company) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('dashboard.shop.categories.partials.fields', ['category' => null])
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
    @foreach ($allCategories as $category)
        <x-modal :name="'category-edit-'.$category->id" title="Edit kategori" max-width="max-w-2xl">
            <form method="POST" action="{{ route('websites.shop.categories.update', [$company, $category]) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                @include('dashboard.shop.categories.partials.fields', ['category' => $category])
                <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </x-modal>
    @endforeach
</x-website-layout>
