@php
    $editing = $template->exists;
    $allSections = config('website-templates.sections');
    $fonts = config('website-templates.fonts');
    $fontOptions = array_combine($fonts, $fonts);
    $currentSections = array_values(array_filter((array) old('settings.sections', $settings['sections'] ?? []), fn ($s) => isset($allSections[$s])));
    $sectionItems = collect($currentSections)->map(fn ($key) => ['key' => $key, 'label' => $allSections[$key], 'on' => true])
        ->concat(collect($allSections)->except($currentSections)->map(fn ($label, $key) => ['key' => $key, 'label' => $label, 'on' => false])->values())
        ->values();
    $radii = collect(config('website-templates.radii'))->mapWithKeys(fn ($px, $key) => [$key => strtoupper($key).' ('.$px.')'])->all();
@endphp
<x-layouts.admin :title="$editing ? 'Edit Template' : 'Template Baru'">
    <x-page-header :title="$editing ? 'Edit: '.$template->name : 'Template Baru'"
        :description="$editing ? 'Perbarui informasi, thumbnail dan branding default template.' : 'Tambahkan template baru berdasarkan salah satu layout yang tersedia.'"
        :back="route('admin.templates.index')">
        @if ($editing)
            <x-slot:actions>
                <a href="{{ route('templates.show', $template) }}" target="_blank" class="btn btn-secondary"><x-icon name="eye" class="size-4" /> Halaman Preview</a>
                <form method="POST" action="{{ route('admin.templates.duplicate', $template) }}">
                    @csrf
                    <button class="btn btn-secondary"><x-icon name="duplicate" class="size-4" /> Duplikat</button>
                </form>
            </x-slot:actions>
        @endif
    </x-page-header>

    <form method="POST" action="{{ $editing ? route('admin.templates.update', $template) : route('admin.templates.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Informasi Template</h2>
                    </div>
                    <div class="card-body grid gap-5 sm:grid-cols-2">
                        <x-form.input name="name" label="Nama template" :value="$template->name" required />
                        <x-form.input name="slug" label="Slug" :value="$template->slug" placeholder="otomatis-dari-nama" help="Huruf kecil, angka dan tanda hubung." />
                        <x-form.select name="layout" label="Layout" :value="$template->layout"
                            :options="collect($layouts)->mapWithKeys(fn ($l, $key) => [$key => $l['name'] ?? $key])->all()"
                            help="Struktur & gaya dasar halaman." />
                        <x-form.select name="template_category_id" label="Kategori" :value="$template->template_category_id"
                            :options="$categories->pluck('name', 'id')->all()" placeholder="— Tanpa kategori —" />
                        <x-form.textarea name="description" label="Deskripsi" :value="$template->description" rows="3" class="sm:col-span-2" />
                        <x-form.input name="style" label="Style keywords" :value="$template->style" placeholder="Enterprise · Clean · Premium" help="Dipisah titik tengah (·) atau koma; tampil sebagai tag di galeri." class="sm:col-span-2" />
                        <x-form.select name="demo" label="Data demo preview" :value="$template->demo" :options="$demoOptions" placeholder="— Sesuai layout —" class="sm:col-span-2" />
                        <x-form.input name="preview_url" type="url" label="Preview URL (opsional)" :value="$template->preview_url" placeholder="https://..." class="sm:col-span-2"
                            help="URL demo eksternal. Kosongkan untuk memakai preview bawaan." />
                    </div>
                </div>

                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Default Branding</h2>
                        <p class="text-sm text-slate-500">Nilai awal untuk website yang memakai template ini. Kosong = mengikuti default layout.</p>
                    </div>
                    <div class="card-body grid gap-5 sm:grid-cols-2">
                        <x-form.color name="settings[primary_color]" label="Warna utama" :value="$settings['primary_color'] ?? '#1d4ed8'" />
                        <x-form.color name="settings[secondary_color]" label="Warna sekunder" :value="$settings['secondary_color'] ?? '#0f172a'" />
                        <x-form.select name="settings[heading_font]" label="Font heading" :value="$settings['heading_font'] ?? null" :options="$fontOptions" placeholder="— Default layout —" />
                        <x-form.select name="settings[body_font]" label="Font body" :value="$settings['body_font'] ?? null" :options="$fontOptions" placeholder="— Default layout —" />
                        <x-form.select name="settings[button_style]" label="Gaya tombol" :value="$settings['button_style'] ?? null" :options="config('website-templates.button_styles')" placeholder="— Default layout —" />
                        <x-form.select name="settings[border_radius]" label="Border radius" :value="$settings['border_radius'] ?? null" :options="$radii" placeholder="— Default layout —" />

                        <div class="sm:col-span-2" x-data="{ items: @js($sectionItems) }">
                            <label class="form-label">Urutan section default</label>
                            <p class="form-help mb-3 mt-0">Centang section yang tampil dan atur urutannya dengan tombol panah.</p>
                            <ul class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200">
                                <template x-for="(item, index) in items" :key="item.key">
                                    <li class="flex items-center gap-3 bg-white px-4 py-2.5" :class="!item.on && 'bg-slate-50'">
                                        <span class="w-5 text-center text-xs font-semibold text-slate-400" x-text="index + 1"></span>
                                        <label class="flex flex-1 cursor-pointer items-center gap-2.5 text-sm">
                                            <input type="checkbox" class="form-checkbox" x-model="item.on">
                                            <span :class="item.on ? 'font-medium text-slate-800' : 'text-slate-400'" x-text="item.label"></span>
                                        </label>
                                        <input type="hidden" name="settings[sections][]" :value="item.key" :disabled="!item.on">
                                        <div class="flex gap-0.5">
                                            <button type="button" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-30" :disabled="index === 0"
                                                @click="items.splice(index - 1, 0, items.splice(index, 1)[0])" title="Naik"><x-icon name="chevron-down" class="size-4 rotate-180" /></button>
                                            <button type="button" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-30" :disabled="index === items.length - 1"
                                                @click="items.splice(index + 1, 0, items.splice(index, 1)[0])" title="Turun"><x-icon name="chevron-down" class="size-4" /></button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            <noscript>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($sectionItems as $item)
                                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="form-checkbox" name="settings[sections][]" value="{{ $item['key'] }}" @checked($item['on'])> {{ $item['label'] }}</label>
                                    @endforeach
                                </div>
                            </noscript>
                            @error('settings.sections')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Component composition & design system (layout "composer") --}}
                @php
                    $composition = old('config', $template->config ?? []);
                @endphp
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Komposisi Komponen & Design System</h2>
                        <p class="text-sm text-slate-500">Berlaku untuk layout <strong>Component Composer</strong>: pilih varian komponen tiap bagian dan token desainnya. Kombinasi inilah yang membuat setiap template berbeda.</p>
                    </div>
                    <div class="card-body space-y-6">
                        <div class="grid gap-4 sm:grid-cols-3">
                            @foreach (\App\Support\Website\DesignSystem::SLOTS as $slot => $default)
                                <x-form.select :name="'config[components]['.$slot.']'" :label="ucwords(str_replace('-', ' ', $slot))" :value="$composition['components'][$slot] ?? $default"
                                    :options="\App\Support\Website\ComponentRegistry::options($slot)" />
                            @endforeach
                        </div>
                        <div class="grid gap-4 border-t border-slate-100 pt-6 sm:grid-cols-3">
                            @foreach (\App\Support\Website\DesignSystem::OPTIONS as $token => $options)
                                <x-form.select :name="'config[design]['.$token.']'" :label="\App\Support\Website\DesignSystem::LABELS[$token]" :value="$composition['design'][$token] ?? $options[0]"
                                    :options="array_combine($options, array_map(fn ($o) => ucwords(str_replace('-', ' ', $o)), $options))" />
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-500">Tip: coba varian tanpa menyimpan lewat URL preview, mis. <code class="rounded bg-slate-100 px-1">{{ $editing ? route('templates.render', $template) : '/templates/{slug}/render' }}?c[hero]=editorial&amp;d[theme]=dark</code></p>
                    </div>
                </div>

                @if ($editing)
                    <div class="card overflow-hidden">
                        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                            <div>
                                <h2 class="font-display text-base font-semibold text-slate-900">Live Preview</h2>
                                <p class="text-sm text-slate-500">Preview dengan konten demo (pengaturan tersimpan).</p>
                            </div>
                            <a href="{{ route('templates.render', $template) }}" target="_blank" class="btn btn-ghost btn-sm"><x-icon name="external" class="size-3.5" /> Layar penuh</a>
                        </div>
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100" x-data="{ s: 0.5 }" x-init="s = $el.clientWidth / 1440; new ResizeObserver(() => s = $el.clientWidth / 1440).observe($el)">
                            <iframe src="{{ route('templates.render', $template) }}" title="Preview {{ $template->name }}" loading="lazy"
                                class="absolute top-0 left-0 origin-top-left border-0" style="width: 1440px; height: 900px; transform: scale(0.5)"
                                :style="`width: 1440px; height: 900px; transform: scale(${s})`"></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Publikasi</h2>
                    </div>
                    <div class="card-body space-y-5">
                        <x-form.select name="status" label="Status" :value="$template->status" :options="['draft' => 'Draft', 'published' => 'Published']"
                            help="Hanya template published yang terlihat oleh pengguna." />
                        <x-form.input name="sort_order" type="number" min="0" label="Urutan" :value="$template->sort_order ?? 0" />
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" class="form-checkbox mt-0.5" @checked(old('is_featured', $template->is_featured))>
                            <span>
                                <span class="block text-sm font-medium text-slate-800">Featured</span>
                                <span class="block text-xs text-slate-500">Ditampilkan menonjol di galeri template.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Thumbnail</h2>
                    </div>
                    <div class="card-body">
                        <x-form.image name="thumbnail" label="Desktop" :value="$template->url('thumbnail')" help="Rasio 16:10 disarankan. Tanpa thumbnail, kartu memakai live preview." />
                        <x-form.image name="mobile_thumbnail" label="Mobile" :value="$template->url('mobile_thumbnail')" aspect="aspect-[9/16]" help="Screenshot mobile (opsional). Bisa dibuat otomatis: php artisan templates:thumbnails" class="mt-5" />
                    </div>
                </div>

                @if ($editing)
                    <div class="card card-body text-sm">
                        <dl class="space-y-2">
                            <div class="flex justify-between"><dt class="text-slate-500">Websites</dt><dd class="font-medium text-slate-800">{{ $template->companyProfiles()->count() }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Users</dt><dd class="font-medium text-slate-800">{{ $template->companyProfiles()->distinct()->count('user_id') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Published websites</dt><dd class="font-medium text-slate-800">{{ $template->companyProfiles()->published()->count() }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Dibuat</dt><dd class="font-medium text-slate-800">{{ $template->created_at?->format('d M Y') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Diperbarui</dt><dd class="font-medium text-slate-800">{{ $template->updated_at?->diffForHumans() }}</dd></div>
                        </dl>
                    </div>
                @endif

                <div class="flex gap-2">
                    <button class="btn btn-primary flex-1"><x-icon name="check" class="size-4" /> {{ $editing ? 'Simpan Template' : 'Buat Template' }}</button>
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </div>
        </div>
    </form>

    @if ($editing)
        <div class="mt-8 flex items-center justify-between rounded-2xl border border-rose-200 bg-rose-50/50 px-6 py-4">
            <div>
                <p class="text-sm font-semibold text-rose-800">Hapus template</p>
                <p class="text-xs text-rose-700">Hanya bisa dihapus jika tidak sedang dipakai website.</p>
            </div>
            <x-confirm-delete :action="route('admin.templates.destroy', $template)" label="Hapus" message="Hapus template ini secara permanen?" />
        </div>
    @endif
</x-layouts.admin>
