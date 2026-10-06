{{-- Corporate hero: split layout, text left, framed photo right with floating stat card. --}}
<section id="hero" class="relative overflow-hidden bg-gradient-to-b from-slate-50 to-white">
    <div class="absolute -top-40 -right-40 size-[36rem] rounded-full bg-primary/10 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 lg:grid-cols-2 lg:py-28">
        <div>
            @if ($company->established_year)
                <span class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3.5 py-1 text-xs font-semibold text-primary">
                    <span class="size-1.5 rounded-full bg-primary"></span> Dipercaya sejak {{ $company->established_year }}
                </span>
            @endif
            <h1 class="mt-6 font-heading text-4xl leading-tight font-extrabold tracking-tight text-slate-900 md:text-5xl lg:text-[3.4rem]">
                {{ $section->title ?: ($company->tagline ?: $company->name) }}
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600">
                {{ $section->subtitle ?: $company->description }}
            </p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-7 py-3.5 text-sm font-semibold text-on-primary shadow-lg shadow-primary/30 transition hover:opacity-90">
                    Konsultasi Sekarang <x-icon name="arrow-right" class="size-4" />
                </a>
                <a href="{{ $site->anchor('services') }}" class="inline-flex items-center gap-2 rounded-btn border border-slate-300 bg-white px-7 py-3.5 text-sm font-semibold text-slate-800 transition hover:border-primary hover:text-primary">
                    Layanan Kami
                </a>
            </div>
            <dl class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-slate-200 pt-8">
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Pengalaman</dt>
                    <dd class="mt-1 font-heading text-2xl font-extrabold text-slate-900">{{ $company->established_year ? (date('Y') - $company->established_year).'+ th' : '10+ th' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Proyek</dt>
                    <dd class="mt-1 font-heading text-2xl font-extrabold text-slate-900">{{ max($company->projects->count() * 25, 50) }}+</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Layanan</dt>
                    <dd class="mt-1 font-heading text-2xl font-extrabold text-slate-900">{{ $company->services->count() ?: '—' }}</dd>
                </div>
            </dl>
        </div>
        <div class="relative">
            <div class="absolute -inset-4 rounded-brand border-2 border-primary/20 lg:-inset-6"></div>
            <x-site.img :src="$company->url('hero_image')" :alt="$company->name" class="relative aspect-[4/3] w-full rounded-brand object-cover shadow-2xl shadow-slate-900/20" icon="building" />
            <div class="absolute -bottom-8 -left-4 hidden w-64 rounded-brand bg-white p-5 shadow-xl shadow-slate-900/10 sm:block">
                <div class="flex items-center gap-3">
                    <span class="inline-flex size-11 items-center justify-center rounded-brand bg-primary text-on-primary"><x-icon name="shield" class="size-6" /></span>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Terpercaya & Profesional</p>
                        <p class="text-xs text-slate-500">{{ $company->city ?: 'Indonesia' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
