<x-website-layout :company="$company" title="Sections">
    <div class="card" x-data="sectionBuilder(@js(route('websites.sections.update', $company)), @js($sections))">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Section Builder</h2>
                <p class="text-xs text-slate-500">Aktifkan, nonaktifkan dan seret untuk mengatur urutan section di halaman utama.</p>
            </div>
            <span class="text-xs font-medium">
                <span x-show="status === 'saving'" class="text-slate-500">Menyimpan…</span>
                <span x-cloak x-show="status === 'saved'" class="text-emerald-600">✓ Tersimpan</span>
                <span x-cloak x-show="status === 'error'" class="text-rose-600">Gagal menyimpan</span>
            </span>
        </div>
        <ul x-ref="list" class="divide-y divide-slate-100">
            <template x-for="section in sections" :key="section.key">
                <li :data-key="section.key" class="bg-white px-4 py-3 sm:px-6" x-data="{ edit: false }">
                    <div class="flex items-center gap-4">
                        <button type="button" data-handle class="cursor-grab text-slate-300 hover:text-slate-500" aria-label="Urutkan"><x-icon name="bars" class="size-5" /></button>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold" :class="section.is_enabled ? 'text-slate-900' : 'text-slate-400'">
                                <span x-text="section.label"></span>
                                <span class="ml-1 font-normal text-slate-400" x-show="section.title" x-text="'— ' + section.title"></span>
                            </p>
                            <p class="text-xs text-amber-600" x-show="!section.has_content && section.is_enabled">Belum ada konten — section tersembunyi sampai Anda menambahkan item.</p>
                        </div>
                        <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="edit = !edit" title="Ubah judul"><x-icon name="pencil" class="size-4" /></button>
                        <button type="button" role="switch" :aria-checked="section.is_enabled" @click="section.is_enabled = !section.is_enabled; save()"
                                class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition" :class="section.is_enabled ? 'bg-brand-600' : 'bg-slate-200'">
                            <span class="inline-block size-5 translate-y-0.5 rounded-full bg-white shadow transition" :class="section.is_enabled ? 'translate-x-5.5' : 'translate-x-0.5'"></span>
                        </button>
                    </div>
                    <div x-show="edit" x-collapse class="mt-3 grid gap-3 pl-9 sm:grid-cols-2">
                        <div><label class="form-label">Judul custom</label><input type="text" x-model="section.title" maxlength="150" class="form-input" placeholder="Default dari template"></div>
                        <div><label class="form-label">Subjudul custom</label><input type="text" x-model="section.subtitle" maxlength="400" class="form-input" placeholder="Default dari template"></div>
                        <div class="sm:col-span-2"><button type="button" class="btn btn-primary btn-sm" @click="save(); edit = false">Simpan judul</button></div>
                    </div>
                </li>
            </template>
        </ul>
    </div>
    <p class="mt-4 text-xs text-slate-500">Header, navigasi dan footer selalu tampil. Urutan default mengikuti template; gunakan “Reset” di menu Template untuk mengembalikannya.</p>
</x-website-layout>
