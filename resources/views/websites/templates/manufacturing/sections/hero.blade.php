{{-- Manufacturing hero: split with spec sheet left and large factory image right. --}}
@php($years = $company->established_year ? date('Y') - $company->established_year : null)
<section id="hero" class="relative overflow-hidden border-b border-slate-200 bg-white">
    <div class="absolute inset-y-0 left-0 w-full bg-[linear-gradient(to_right,rgb(15_23_42/0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgb(15_23_42/0.04)_1px,transparent_1px)] bg-[size:48px_48px] lg:w-1/2 pointer-events-none"></div>
    <div class="mx-auto max-w-7xl">
        <div class="relative px-6 py-16 lg:w-1/2 lg:py-24 lg:pr-14">
            <p class="inline-flex items-center gap-3 font-mono text-xs font-semibold tracking-widest text-primary uppercase">
                <span class="h-px w-8 bg-primary"></span> {{ $company->city ? 'Manufaktur · '.$company->city : 'Manufaktur Indonesia' }}
            </p>
            <h1 class="mt-6 font-heading text-4xl leading-[1.05] font-bold tracking-tight text-slate-900 uppercase md:text-5xl lg:text-6xl">
                {{ $section->title ?: ($company->tagline ?: $company->name) }}
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600">{{ $section->subtitle ?: $company->description }}</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-7 py-4 text-xs font-bold tracking-wider text-on-primary uppercase transition hover:opacity-90">Request Quote <x-icon name="arrow-right" class="size-4" /></a>
                <a href="{{ $site->anchor('products') }}" class="inline-flex items-center gap-2 rounded-btn border-2 border-slate-900 px-7 py-4 text-xs font-bold tracking-wider text-slate-900 uppercase transition hover:bg-slate-900 hover:text-white"><x-icon name="cube" class="size-4" /> Katalog Produk</a>
            </div>

            <dl class="mt-12 grid grid-cols-2 border-t border-l border-slate-200 sm:grid-cols-4">
                @foreach ([
                    ['Pengalaman', $years ? $years.'+' : '15+', 'tahun'],
                    ['Lini Produk', $company->products->count() ?: '—', 'SKU utama'],
                    ['Kapasitas', number_format(max($company->products->count(), 1) * 1250, 0, ',', '.'), 'unit / bln'],
                    ['Standar', 'ISO', '9001 · 14001'],
                ] as [$label, $value, $unit])
                    <div class="border-r border-b border-slate-200 bg-white/80 p-4">
                        <dt class="font-mono text-[10px] tracking-widest text-slate-500 uppercase">{{ $label }}</dt>
                        <dd class="mt-2 font-heading text-2xl font-bold text-slate-900">{{ $value }}</dd>
                        <dd class="font-mono text-[11px] text-primary">{{ $unit }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <div class="relative min-h-80 sm:min-h-[28rem] lg:absolute lg:inset-y-0 lg:right-0 lg:min-h-0 lg:w-1/2">
            <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="absolute inset-0 size-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-secondary/70 via-transparent to-transparent"></div>
            <div class="absolute top-6 right-6 hidden bg-primary px-4 py-3 text-on-primary sm:block">
                <p class="font-mono text-[10px] tracking-widest uppercase opacity-80">Est.</p>
                <p class="font-heading text-2xl font-bold">{{ $company->established_year ?: '—' }}</p>
            </div>
            <div class="absolute inset-x-6 bottom-6 flex flex-wrap items-center gap-2">
                @foreach (['Quality Control 100%', 'Pengiriman Nasional', 'OEM / ODM'] as $chip)
                    <span class="inline-flex items-center gap-1.5 bg-white/95 px-3 py-1.5 font-mono text-[11px] font-semibold text-slate-800 uppercase"><x-icon name="check" class="size-3.5 text-primary" /> {{ $chip }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>
