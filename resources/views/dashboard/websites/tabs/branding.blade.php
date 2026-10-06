@php($brand = $company->brand())
<div class="space-y-6">
    <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
        Template <strong>{{ $company->template?->name }}</strong> tetap menentukan struktur dasar. Di sini Anda menyesuaikan identitas brand.
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <x-form.color name="branding[primary_color]" label="Primary color" :value="$brand['primary_color']" />
        <x-form.color name="branding[secondary_color]" label="Secondary color" :value="$brand['secondary_color']" />
        <x-form.select name="branding[heading_font]" label="Font judul" :options="array_combine(config('website-templates.fonts'), config('website-templates.fonts'))" :value="$brand['heading_font']" />
        <x-form.select name="branding[body_font]" label="Font isi" :options="array_combine(config('website-templates.fonts'), config('website-templates.fonts'))" :value="$brand['body_font']" />
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <p class="form-label">Button style</p>
            <div class="grid grid-cols-3 gap-2">
                @foreach (config('website-templates.button_styles') as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="branding[button_style]" value="{{ $value }}" class="peer sr-only" @checked(old('branding.button_style', $brand['button_style']) === $value)>
                        <span class="flex flex-col items-center gap-2 rounded-xl border border-slate-200 p-3 text-xs font-medium peer-checked:border-brand-500 peer-checked:ring-2 peer-checked:ring-brand-500/20">
                            <span class="bg-slate-800 px-3 py-1 text-[10px] text-white {{ ['pill' => 'rounded-full', 'square' => 'rounded-none', 'rounded' => 'rounded-md'][$value] }}">Button</span>{{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <p class="form-label">Border radius</p>
            <div class="grid grid-cols-5 gap-2">
                @foreach (config('website-templates.radii') as $value => $px)
                    <label class="cursor-pointer">
                        <input type="radio" name="branding[border_radius]" value="{{ $value }}" class="peer sr-only" @checked(old('branding.border_radius', $brand['border_radius']) === $value)>
                        <span class="flex flex-col items-center gap-1.5 rounded-xl border border-slate-200 p-2 text-[11px] font-medium uppercase peer-checked:border-brand-500 peer-checked:ring-2 peer-checked:ring-brand-500/20">
                            <span class="size-6 border-2 border-slate-700" style="border-radius: {{ $px }}"></span>{{ $value }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    <div class="grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
        <x-form.image name="logo" label="Logo" :value="$company->url('logo')" aspect="aspect-square" />
        <x-form.image name="favicon" label="Favicon" :value="$company->url('favicon')" aspect="aspect-square" />
        <x-form.image name="hero_image" label="Gambar hero" :value="$company->url('hero_image')" class="sm:col-span-2" help="Gambar utama di bagian atas website. Rekomendasi 1600×900 px." />
    </div>
</div>
