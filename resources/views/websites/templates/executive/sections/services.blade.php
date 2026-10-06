{{-- Executive services: 2x3 grid of hairline cells with gold line icons. --}}
<section id="services" class="relative bg-secondary py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="flex items-center justify-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary/70"></span> Layanan <span class="h-px w-10 bg-primary/70"></span></p>
            <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-on-secondary md:text-5xl">{{ $section->title ?: 'Keahlian yang Membentuk Nilai' }}</h2>
            <p class="mt-5 text-on-secondary/60">{{ $section->subtitle ?: 'Solusi yang dirancang dengan presisi untuk institusi, keluarga, dan pemimpin bisnis.' }}</p>
        </div>
        <div class="mt-20 grid border-t border-l border-primary/20 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->services as $service)
                <article class="group relative border-r border-b border-primary/20 p-10 transition duration-500 hover:bg-white/[0.03] lg:p-12">
                    <span class="absolute top-0 left-0 h-px w-0 bg-primary transition-all duration-700 group-hover:w-full"></span>
                    <div class="flex items-start justify-between">
                        <x-icon :name="$service->icon ?: 'briefcase'" class="size-10 text-primary" stroke="1" />
                        <span class="font-heading text-lg text-on-secondary/30 italic">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-10 font-heading text-2xl font-medium text-on-secondary">{{ $service->title }}</h3>
                    <p class="mt-4 text-sm leading-relaxed text-on-secondary/60">{{ $service->description }}</p>
                    <a href="{{ $site->anchor('contact') }}" class="mt-8 inline-flex items-center gap-3 text-[10px] font-medium tracking-[0.3em] text-primary uppercase opacity-70 transition group-hover:opacity-100">Selengkapnya <span class="h-px w-6 bg-primary transition-all group-hover:w-10"></span></a>
                </article>
            @endforeach
        </div>
    </div>
</section>
