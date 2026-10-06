<x-layouts.admin title="Settings">
    <x-page-header title="Settings" description="Pengaturan global aplikasi, SEO default dan penyimpanan." />

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Aplikasi</h2>
                        <p class="text-sm text-slate-500">Identitas platform yang tampil di landing page, email dan dashboard.</p>
                    </div>
                    <div class="card-body grid gap-5 sm:grid-cols-2">
                        <x-form.input name="app_name" label="Application Name" :value="$settings['app_name'] ?? config('app.name')" required maxlength="60" />
                        <x-form.input name="support_email" type="email" label="Support Email" :value="$settings['support_email'] ?? null" />
                        <x-form.input name="app_tagline" label="Tagline" :value="$settings['app_tagline'] ?? null" maxlength="120" class="sm:col-span-2" />
                        <x-form.image name="app_logo" label="Logo" :value="\App\Support\MediaUrl::resolve($settings['app_logo'] ?? null)" aspect="aspect-[3/1]" help="PNG transparan disarankan." />
                        <x-form.image name="app_favicon" label="Favicon" :value="\App\Support\MediaUrl::resolve($settings['app_favicon'] ?? null)" aspect="aspect-square" help="Persegi, minimal 64×64 px." />
                    </div>
                </div>

                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">SEO Default</h2>
                        <p class="text-sm text-slate-500">Dipakai pada halaman publik platform.</p>
                    </div>
                    <div class="card-body space-y-5">
                        <x-form.input name="default_seo_title" label="Meta title" :value="$settings['default_seo_title'] ?? null" maxlength="120" />
                        <x-form.textarea name="default_seo_description" label="Meta description" :value="$settings['default_seo_description'] ?? null" rows="3" maxlength="500" />
                        <x-form.input name="default_seo_keywords" label="Keywords" :value="$settings['default_seo_keywords'] ?? null" help="Pisahkan dengan koma." />
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Website Baru</h2>
                    </div>
                    <div class="card-body space-y-5">
                        <x-form.select name="default_template" label="Default Template" :value="$settings['default_template'] ?? null"
                            :options="$templates->pluck('name', 'slug')->all()" placeholder="— Tidak ada —" help="Template terpilih saat pengguna membuat website." />
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3">
                            <input type="checkbox" name="registration_enabled" value="1" class="form-checkbox mt-0.5" @checked(old('registration_enabled', $settings['registration_enabled'] ?? '1') == '1')>
                            <span>
                                <span class="block text-sm font-medium text-slate-800">Pendaftaran dibuka</span>
                                <span class="block text-xs text-slate-500">Izinkan pengunjung membuat akun baru.</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="card">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="font-display text-base font-semibold text-slate-900">Storage</h2>
                    </div>
                    <div class="card-body space-y-5">
                        <x-form.select name="media_disk" label="Media disk" :value="$settings['media_disk'] ?? 'public'" :options="array_combine($disks, $disks)" help="Disk Laravel untuk menyimpan unggahan baru." />
                        <x-form.input name="max_upload_kb" type="number" min="256" max="20480" label="Maks. ukuran upload" suffix="KB" :value="$settings['max_upload_kb'] ?? 4096" required />
                    </div>
                </div>

                <button class="btn btn-primary w-full"><x-icon name="check" class="size-4" /> Simpan Pengaturan</button>
            </div>
        </div>
    </form>

    <div class="card mt-8 overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="font-display text-base font-semibold text-slate-900">System Configuration</h2>
            <p class="text-sm text-slate-500">Informasi read-only dari konfigurasi server (.env).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <tbody class="divide-y divide-slate-100">
                    @foreach ($system as $label => $value)
                        <tr>
                            <td class="w-1/3 font-medium whitespace-nowrap text-slate-600">{{ $label }}</td>
                            <td class="font-mono text-sm text-slate-900">{{ $value ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
