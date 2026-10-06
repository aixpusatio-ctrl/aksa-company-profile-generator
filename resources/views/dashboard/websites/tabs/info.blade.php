<div class="space-y-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-form.input name="name" label="Nama perusahaan" :value="$company->name" required class="sm:col-span-2" />
        <x-form.input name="tagline" label="Tagline" :value="$company->tagline" placeholder="Contoh: Solusi Konstruksi Terpercaya" class="sm:col-span-2" />
        <x-form.textarea name="description" label="Deskripsi singkat" :value="$company->description" rows="3" help="Ditampilkan di hero dan footer website. 1–3 kalimat." class="sm:col-span-2" />
        <x-form.input name="established_year" type="number" label="Tahun berdiri" :value="$company->established_year" min="1800" :max="date('Y') + 1" />
        <x-form.input name="website" type="url" label="Website lama (opsional)" :value="$company->website" placeholder="https://" />
        <x-form.input name="phone" label="Telepon" :value="$company->phone" />
        <x-form.input name="email" type="email" label="Email" :value="$company->email" />
        <x-form.input name="whatsapp" label="WhatsApp" :value="$company->whatsapp" placeholder="0812xxxxxxx" help="Menampilkan tombol chat WhatsApp di website." />
    </div>
    <div class="grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
        <x-form.image name="logo" label="Logo" :value="$company->url('logo')" aspect="aspect-square" />
        <x-form.image name="favicon" label="Favicon" :value="$company->url('favicon')" aspect="aspect-square" help="Gambar persegi, minimal 32×32 px." />
    </div>
    <div class="border-t border-slate-100 pt-6">
        <p class="text-sm font-semibold text-slate-900">Social media</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            @foreach (\App\Models\CompanyProfile::SOCIAL_NETWORKS as $key => $label)
                <div>
                    <label class="form-label flex items-center gap-2" for="f-social_links-{{ $key }}"><x-icon :name="$key" class="size-4 text-slate-400" /> {{ $label }}</label>
                    <input id="f-social_links-{{ $key }}" type="url" name="social_links[{{ $key }}]" value="{{ old('social_links.'.$key, $company->social_links[$key] ?? '') }}" placeholder="https://" class="form-input">
                    @error('social_links.'.$key)<p class="form-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>
    </div>
</div>
