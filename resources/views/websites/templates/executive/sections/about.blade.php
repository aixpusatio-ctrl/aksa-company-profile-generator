{{-- Executive about: ivory band, framed image with gold offset, serif statement, vision quote. --}}
<section id="about" class="bg-stone-50 py-24 text-slate-600 lg:py-32">
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2 lg:gap-24">
        <div class="relative order-last lg:order-first">
            <div class="absolute -top-5 -left-5 h-full w-full border border-primary/60 md:-top-8 md:-left-8"></div>
            <x-site.img :src="$company->gallery->first()?->url('image') ?? $company->url('hero_image')" :alt="$company->name" icon="building" class="relative aspect-[4/5] w-full object-cover" />
            @if ($company->established_year)
                <div class="absolute -right-3 -bottom-8 bg-secondary px-8 py-7 text-center md:-right-8">
                    <p class="text-[10px] tracking-[0.35em] text-primary uppercase">Sejak</p>
                    <p class="mt-1 font-heading text-5xl text-on-secondary">{{ $company->established_year }}</p>
                </div>
            @endif
        </div>
        <div>
            <p class="flex items-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Tentang Kami</p>
            <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-secondary md:text-5xl">{{ $section->title ?: 'Warisan Kepercayaan, Visi Masa Depan' }}</h2>
            @if ($section->subtitle)
                <p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>
            @endif
            <div class="site-prose mt-8 text-[15px] text-slate-600">{!! $company->about ?: e($company->description) !!}</div>

            @if ($company->vision)
                <blockquote class="mt-10 border-l border-primary pl-6 font-heading text-2xl leading-snug text-secondary italic">“{{ $company->vision }}”</blockquote>
            @endif

            @if ($company->valueItems())
                <dl class="mt-10 grid gap-x-8 gap-y-5 border-t border-slate-200 pt-8 sm:grid-cols-2">
                    @foreach (array_slice($company->valueItems(), 0, 4) as $value)
                        <div class="flex gap-3 text-sm"><span class="mt-2 size-1.5 shrink-0 rotate-45 bg-primary"></span> <span>{{ $value }}</span></div>
                    @endforeach
                </dl>
            @endif
        </div>
    </div>
</section>
