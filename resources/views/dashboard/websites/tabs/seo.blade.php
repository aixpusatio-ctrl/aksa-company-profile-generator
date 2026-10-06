<div class="space-y-6" x-data="{ title: @js(old('seo_title', $company->seo_title) ?? ''), desc: @js(old('seo_description', $company->seo_description) ?? '') }">
    <div class="rounded-xl border border-slate-200 p-4">
        <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase">Pratinjau Google</p>
        <p class="mt-2 text-xs text-emerald-700">{{ $company->publicUrl() }}</p>
        <p class="truncate text-lg text-[#1a0dab]" x-text="title || @js($company->name.($company->tagline ? ' — '.$company->tagline : ''))"></p>
        <p class="line-clamp-2 text-sm text-slate-600" x-text="desc || @js(\Illuminate\Support\Str::limit((string) $company->description, 160))"></p>
    </div>
    <div>
        <x-form.input name="seo_title" label="SEO title" :value="$company->seo_title" x-model="title" maxlength="120" />
        <p class="form-help"><span x-text="title.length"></span>/60 karakter direkomendasikan.</p>
    </div>
    <div>
        <x-form.textarea name="seo_description" label="SEO description" :value="$company->seo_description" rows="3" x-model="desc" maxlength="500" />
        <p class="form-help"><span x-text="desc.length"></span>/160 karakter direkomendasikan.</p>
    </div>
    <x-form.input name="seo_keywords" label="SEO keywords" :value="$company->seo_keywords" placeholder="kontraktor, konstruksi, surabaya" help="Pisahkan dengan koma." />
    <div class="grid gap-5 border-t border-slate-100 pt-6 sm:grid-cols-2">
        <x-form.input name="og_title" label="Open Graph title" :value="$company->og_title" help="Judul saat dibagikan di media sosial." />
        <x-form.textarea name="og_description" label="Open Graph description" :value="$company->og_description" rows="2" />
        <x-form.image name="og_image" label="Open Graph image" :value="$company->url('og_image')" class="sm:col-span-2" help="1200×630 px direkomendasikan." />
    </div>
    <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
        <p class="font-semibold text-slate-900">Otomatis dibuat</p>
        <ul class="mt-2 grid gap-1 sm:grid-cols-2">
            <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> sitemap.xml</li>
            <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> robots.txt</li>
            <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> Canonical URL</li>
            <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> Open Graph & Twitter Card</li>
        </ul>
    </div>
</div>
