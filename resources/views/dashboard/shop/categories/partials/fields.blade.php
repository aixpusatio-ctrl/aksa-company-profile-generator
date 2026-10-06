{{-- Category form fields. $category may be null (create). --}}
@php($hasChildren = $category && $category->relationLoaded('children') && $category->children->isNotEmpty())
<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="form-label">Nama kategori <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ $category?->name }}" required maxlength="100" class="form-input">
    </div>
    <div>
        <label class="form-label">Slug</label>
        <input type="text" name="slug" value="{{ $category?->slug }}" maxlength="100" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="otomatis" class="form-input">
    </div>
    <div>
        <label class="form-label">Induk kategori</label>
        <select name="parent_id" class="form-input" @disabled($hasChildren)>
            <option value="">— Kategori utama —</option>
            @foreach ($roots as $root)
                @continue($category && $root->id === $category->id)
                <option value="{{ $root->id }}" @selected($category?->parent_id === $root->id)>{{ $root->name }}</option>
            @endforeach
        </select>
        @if ($hasChildren)<p class="form-help">Kategori ini punya sub kategori, jadi tetap kategori utama.</p>@endif
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input">
            <option value="active" @selected(($category?->status ?? 'active') === 'active')>Aktif</option>
            <option value="inactive" @selected($category?->status === 'inactive')>Nonaktif</option>
        </select>
    </div>
    <div>
        <label class="form-label">Urutan</label>
        <input type="number" name="sort_order" min="0" value="{{ $category?->sort_order ?? 0 }}" class="form-input">
    </div>
    <div class="sm:col-span-2">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="2" maxlength="1000" class="form-input">{{ $category?->description }}</textarea>
    </div>
    <x-form.image name="image" label="Gambar kategori" :value="$category?->url('image')" aspect="aspect-square" class="sm:col-span-2" />
</div>
