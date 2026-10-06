<x-website-layout :company="$company" title="Menu">
    @php
        $roots = $tree->map(fn ($m) => ['id' => $m->id, 'title' => $m->title])->values();
    @endphp
    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="card" x-data="menuBuilder(@js(route('websites.menus.tree', $company)))">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Navigation / Sub Menu Builder</h2>
                    <p class="text-xs text-slate-500">Seret untuk mengurutkan. Seret ke dalam area menu lain untuk menjadikannya submenu (maks. 2 level).</p>
                </div>
                <span class="text-xs font-medium">
                    <span x-show="status === 'saving'" class="text-slate-500">Menyimpan…</span>
                    <span x-cloak x-show="status === 'saved'" class="text-emerald-600">✓ Tersimpan</span>
                    <span x-cloak x-show="status === 'error'" class="text-rose-600">Gagal menyimpan</span>
                </span>
            </div>
            <div class="p-4 sm:p-6">
                @if ($tree->isEmpty())
                    <x-empty-state title="Menu masih kosong" description="Tambahkan menu pertama di panel sebelah kanan." icon="list" />
                @endif
                <ul data-sortable data-level="1" class="space-y-2">
                    @foreach ($tree as $menu)
                        <li data-id="{{ $menu->id }}" class="rounded-xl border border-slate-200 bg-white">
                            @include('dashboard.menus.item', ['menu' => $menu])
                            <ul data-sortable data-level="2" class="ml-8 min-h-3 space-y-2 pr-3 pb-3 empty:pb-1">
                                @foreach ($menu->children as $child)
                                    <li data-id="{{ $child->id }}" class="rounded-lg border border-slate-200 bg-slate-50">
                                        @include('dashboard.menus.item', ['menu' => $child])
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card card-body self-start">
            <h2 class="text-base font-semibold text-slate-900">Tambah menu</h2>
            @include('dashboard.menus.form', ['menu' => null, 'action' => route('websites.menus.store', $company)])
        </div>
    </div>

    @foreach ($tree->flatMap(fn ($m) => collect([$m])->merge($m->children)) as $menu)
        <x-modal :name="'menu-'.$menu->id" title="Edit menu">
            @include('dashboard.menus.form', ['menu' => $menu, 'action' => route('websites.menus.update', [$company, $menu])])
        </x-modal>
    @endforeach
</x-website-layout>
