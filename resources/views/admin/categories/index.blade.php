<x-layouts.admin title="Template Categories">
    <x-page-header title="Template Categories" description="Kelompokkan template agar mudah ditemukan pengguna di galeri." :back="route('admin.templates.index')" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            @if ($categories->isEmpty())
                <x-empty-state icon="tag" title="Belum ada kategori" description="Tambahkan kategori pertama melalui formulir di samping." />
            @else
                <div class="card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="w-16">Urutan</th>
                                    <th>Kategori</th>
                                    <th>Template</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($categories as $category)
                                    <tr>
                                        <td class="text-slate-500">{{ $category->sort_order }}</td>
                                        <td>
                                            <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                            <p class="text-xs text-slate-500"><span class="font-mono">{{ $category->slug }}</span>@if ($category->description) · {{ $category->description }}@endif</p>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.templates.index', ['category' => $category->id]) }}" class="badge badge-slate hover:bg-slate-200">{{ $category->templates_count }} template</a>
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-end gap-1">
                                                <button type="button" x-data @click="$dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                                                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                                                <x-confirm-delete :action="route('admin.categories.destroy', $category)" message="Hapus kategori ini? Template di dalamnya akan menjadi tanpa kategori." />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @foreach ($categories as $category)
                    <x-modal name="edit-category-{{ $category->id }}" title="Edit Kategori">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="form-label" for="cat-name-{{ $category->id }}">Nama <span class="text-rose-500">*</span></label>
                                <input id="cat-name-{{ $category->id }}" name="name" value="{{ $category->name }}" required maxlength="100" class="form-input">
                            </div>
                            <div class="grid gap-4 sm:grid-cols-[1fr_7rem]">
                                <div>
                                    <label class="form-label" for="cat-slug-{{ $category->id }}">Slug</label>
                                    <input id="cat-slug-{{ $category->id }}" name="slug" value="{{ $category->slug }}" maxlength="100" class="form-input font-mono">
                                </div>
                                <div>
                                    <label class="form-label" for="cat-order-{{ $category->id }}">Urutan</label>
                                    <input id="cat-order-{{ $category->id }}" type="number" min="0" name="sort_order" value="{{ $category->sort_order }}" class="form-input">
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="cat-desc-{{ $category->id }}">Deskripsi</label>
                                <textarea id="cat-desc-{{ $category->id }}" name="description" rows="2" maxlength="255" class="form-input">{{ $category->description }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" class="btn btn-ghost" @click="open = false">Batal</button>
                                <button class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </x-modal>
                @endforeach
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="card lg:sticky lg:top-24">
                @csrf
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Tambah Kategori</h2>
                </div>
                <div class="card-body space-y-4">
                    <x-form.input name="name" label="Nama" required maxlength="100" />
                    <x-form.input name="slug" label="Slug" placeholder="otomatis-dari-nama" help="Kosongkan untuk dibuat otomatis." />
                    <x-form.input name="sort_order" type="number" min="0" label="Urutan" :value="($categories->max('sort_order') ?? 0) + 1" />
                    <x-form.textarea name="description" label="Deskripsi" rows="2" />
                    <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4" /> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
