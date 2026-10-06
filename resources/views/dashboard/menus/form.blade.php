<form method="POST" action="{{ $action }}" class="mt-4 space-y-4" x-data="{ type: @js(old('type', $menu?->type ?? 'anchor')) }">
    @csrf
    @if ($menu) @method('PUT') @endif
    <x-form.input name="title" label="Judul" :value="$menu?->title" required maxlength="80" :id="'menu-title-'.($menu?->id ?? 'new')" />
    <div>
        <label class="form-label">Tipe</label>
        <select name="type" x-model="type" class="form-input">
            @foreach ($types as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div x-show="type === 'anchor'">
        <label class="form-label">Section</label>
        <select name="url" class="form-input" :disabled="type !== 'anchor'">
            @foreach ($sections as $key => $label)
                <option value="{{ $key }}" @selected(($menu?->type === 'anchor' ? $menu->url : null) === $key)>{{ $label }} (#{{ $key }})</option>
            @endforeach
        </select>
    </div>
    <div x-show="type === 'page'" x-cloak>
        <label class="form-label">Halaman</label>
        <select name="company_page_id" class="form-input" :disabled="type !== 'page'">
            @forelse ($pages as $page)
                <option value="{{ $page->id }}" @selected($menu?->company_page_id === $page->id)>{{ $page->title }} {{ $page->isPublished() ? '' : '(draft)' }}</option>
            @empty
                <option value="">Belum ada halaman</option>
            @endforelse
        </select>
        @if ($pages->isEmpty())<p class="form-help"><a href="{{ route('websites.pages.create', $company) }}" class="text-brand-600 underline">Buat halaman</a> terlebih dahulu.</p>@endif
    </div>
    <div x-show="type === 'url'" x-cloak>
        <label class="form-label">URL eksternal</label>
        <input type="url" name="url" value="{{ $menu?->type === 'url' ? $menu->url : '' }}" placeholder="https://" class="form-input" :disabled="type !== 'url'">
        <label class="mt-2 flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="open_in_new_tab" value="1" class="form-checkbox" @checked($menu?->open_in_new_tab)> Buka di tab baru</label>
    </div>
    @php($parents = $tree->reject(fn ($m) => $menu && $m->id === $menu->id))
    @if (! $menu || $menu->children->isEmpty())
        <x-form.select name="parent_id" label="Parent (submenu dari)" :options="$parents->pluck('title', 'id')->all()" :value="$menu?->parent_id" placeholder="— Menu utama —" />
    @endif
    <x-form.select name="status" label="Status" :options="['active' => 'Tampil', 'inactive' => 'Sembunyikan']" :value="$menu?->status ?? 'active'" />
    <button class="btn btn-primary w-full">{{ $menu ? 'Simpan' : 'Tambah menu' }}</button>
</form>
