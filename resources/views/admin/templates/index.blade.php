<x-layouts.admin title="Templates">
    <x-page-header title="Templates" :description="$templates->count().' template · '.$templates->where('status', 'published')->count().' published · '.\App\Support\Website\ComponentRegistry::count().' komponen di library'">
        <x-slot:actions>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary"><x-icon name="tag" class="size-4" /> Kategori</a>
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Template Baru</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama template..." class="form-input pl-9">
        </div>
        <select name="category" class="form-input sm:w-52">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if (request('q') || request('category'))
                <a href="{{ route('admin.templates.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($templates->isEmpty())
        <x-empty-state icon="template" title="Belum ada template" description="Buat template pertama atau ubah filter pencarian.">
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Template Baru</a>
        </x-empty-state>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($templates as $template)
                <div class="card flex flex-col overflow-hidden">
                    <div class="relative aspect-[16/10] overflow-hidden border-b border-slate-100 bg-slate-100">
                        <x-template-thumb :template="$template" aspect="aspect-auto" class="absolute inset-0" />
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <x-status-badge :status="$template->status" class="shadow-sm" />
                        </div>
                        <form method="POST" action="{{ route('admin.templates.feature', $template) }}" class="absolute top-3 right-3">
                            @csrf
                            <button class="inline-flex size-8 items-center justify-center rounded-full bg-white/90 shadow-sm backdrop-blur {{ $template->is_featured ? 'text-amber-500' : 'text-slate-300 hover:text-amber-400' }}"
                                title="{{ $template->is_featured ? 'Lepas featured' : 'Jadikan featured' }}">
                                <x-icon name="star" class="size-4 {{ $template->is_featured ? 'fill-current' : '' }}" />
                            </button>
                        </form>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-display text-base font-semibold text-slate-900">{{ $template->name }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $template->isComposed() ? 'Composed' : 'Crafted · '.$template->layoutName() }} · {{ $template->category?->name ?? 'Tanpa kategori' }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <span class="badge badge-slate" title="Website yang memakai template ini"><x-icon name="globe" class="size-3" /> {{ $template->company_profiles_count }}</span>
                                <span class="badge badge-slate" title="User yang memakai template ini"><x-icon name="users" class="size-3" /> {{ $template->users_count ?? 0 }}</span>
                            </div>
                        </div>
                        @if ($template->description)
                            <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ $template->description }}</p>
                        @endif
                        <div class="mt-auto flex items-center justify-between gap-2 pt-5">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-primary btn-sm"><x-icon name="pencil" class="size-3.5" /> Edit</a>
                                <form method="POST" action="{{ route('admin.templates.publish', $template) }}">
                                    @csrf
                                    <button class="btn btn-secondary btn-sm">{{ $template->isPublished() ? 'Unpublish' : 'Publish' }}</button>
                                </form>
                            </div>
                            <div class="flex items-center">
                                <a href="{{ route('templates.show', $template) }}" target="_blank" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Preview"><x-icon name="eye" class="size-4" /></a>
                                <form method="POST" action="{{ route('admin.templates.duplicate', $template) }}">
                                    @csrf
                                    <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Duplikat"><x-icon name="duplicate" class="size-4" /></button>
                                </form>
                                <x-confirm-delete :action="route('admin.templates.destroy', $template)" message="Hapus template ini? Template yang sedang dipakai website tidak dapat dihapus." />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
