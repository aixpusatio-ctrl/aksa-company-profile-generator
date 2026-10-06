{{-- Consulting hero: large serif statement with a small framed portrait image. --}}
<section id="hero" class="relative">
    <div class="mx-auto max-w-7xl px-6 pt-16 pb-20 md:pt-24 lg:pb-28">
        <div class="grid gap-12 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-9">
                <p class="flex items-center gap-4 text-xs tracking-[0.3em] text-stone-400 uppercase">
                    <span class="h-px w-10 bg-primary"></span>
                    {{ $company->established_year ? 'Sejak '.$company->established_year : 'Konsultan Bisnis' }}{{ $company->city ? ' · '.$company->city : '' }}
                </p>
                <h1 class="mt-8 font-heading text-5xl leading-[1.08] font-normal tracking-tight text-stone-900 sm:text-6xl lg:text-7xl xl:text-[5.5rem]">
                    {{ $section->title ?: ($company->tagline ?: $company->name) }}
                </h1>
            </div>
            <div class="lg:col-span-3">
                <div class="relative mx-auto max-w-56 lg:max-w-none">
                    <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="user" class="aspect-[3/4] w-full object-cover" />
                    <span class="absolute -top-3 -left-3 size-12 border-t border-l border-primary"></span>
                    <span class="absolute -right-3 -bottom-3 size-12 border-r border-b border-primary"></span>
                </div>
            </div>
        </div>
        <div class="mt-16 grid gap-10 border-t border-stone-200 pt-10 md:grid-cols-12">
            <p class="text-lg leading-relaxed text-stone-600 md:col-span-6">{{ $section->subtitle ?: $company->description }}</p>
            <div class="flex flex-wrap items-start gap-6 md:col-span-5 md:col-start-8 md:justify-end">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-3 rounded-btn bg-primary px-7 py-4 text-sm tracking-wide text-on-primary transition hover:opacity-90">Jadwalkan Konsultasi <x-icon name="arrow-right" class="size-4" /></a>
                <a href="{{ $site->anchor('services') }}" class="inline-flex items-center gap-2 py-4 text-sm tracking-wide text-stone-900 underline decoration-stone-300 underline-offset-8 transition hover:decoration-stone-900">Pendekatan kami</a>
            </div>
        </div>
    </div>
</section>
