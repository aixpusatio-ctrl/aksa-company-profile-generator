{{-- Executive portfolio: wide cinematic rows, image and text alternating. --}}
<section id="projects" class="relative bg-secondary py-24 lg:py-32">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="flex flex-col items-start justify-between gap-8 border-b border-primary/20 pb-12 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="flex items-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Portofolio</p>
                <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-on-secondary md:text-5xl">{{ $section->title ?: 'Mandat Pilihan' }}</h2>
            </div>
            <p class="max-w-md text-on-secondary/60">{{ $section->subtitle ?: 'Transaksi dan penugasan yang mencerminkan kedalaman keahlian serta komitmen kami terhadap hasil.' }}</p>
        </div>

        <div class="mt-16 space-y-20 lg:space-y-28">
            @foreach ($company->projects as $project)
                @php($even = $loop->even)
                <article class="group grid items-center lg:grid-cols-12">
                    <div class="relative lg:col-span-8 lg:row-start-1 {{ $even ? 'lg:col-start-5' : 'lg:col-start-1' }}">
                        <div class="overflow-hidden">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[21/10] w-full object-cover transition duration-[1500ms] group-hover:scale-105" />
                        </div>
                        <div class="pointer-events-none absolute inset-3 border border-primary/0 transition duration-700 group-hover:border-primary/50"></div>
                    </div>
                    <div class="relative z-10 mx-4 -mt-20 sm:mx-10 lg:mx-0 lg:mt-0 lg:col-span-5 lg:row-start-1 {{ $even ? 'lg:col-start-1' : 'lg:col-start-8' }}">
                        <div class="border border-primary/25 bg-secondary p-8 shadow-2xl shadow-black/40 md:p-10">
                            <div class="flex items-center gap-4 text-[10px] tracking-[0.3em] text-primary uppercase">
                                <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="h-px w-8 bg-primary/50"></span>
                                <span>{{ $project->category ?: 'Proyek' }}</span>
                            </div>
                            <h3 class="mt-5 font-heading text-3xl leading-tight font-medium text-on-secondary">{{ $project->title }}</h3>
                            <p class="mt-4 text-sm leading-relaxed text-on-secondary/60">{{ $project->description }}</p>
                            <dl class="mt-8 grid grid-cols-3 gap-4 border-t border-primary/15 pt-6 text-xs">
                                <div><dt class="tracking-[0.2em] text-on-secondary/40 uppercase">Klien</dt><dd class="mt-1.5 text-on-secondary/85">{{ $project->client ?: '—' }}</dd></div>
                                <div><dt class="tracking-[0.2em] text-on-secondary/40 uppercase">Lokasi</dt><dd class="mt-1.5 text-on-secondary/85">{{ $project->location ?: '—' }}</dd></div>
                                <div><dt class="tracking-[0.2em] text-on-secondary/40 uppercase">Tahun</dt><dd class="mt-1.5 text-on-secondary/85">{{ $project->year ?: '—' }}</dd></div>
                            </dl>
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="mt-8 inline-flex items-center gap-3 text-[10px] font-medium tracking-[0.3em] text-primary uppercase">Lihat Detail <x-icon name="arrow-up-right" class="size-3.5" /></a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
