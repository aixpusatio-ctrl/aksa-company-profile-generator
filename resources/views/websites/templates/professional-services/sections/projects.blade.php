{{-- Professional services portfolio: case-study list rows (challenge → outcome). --}}
<section id="projects" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-6 border-b border-slate-200 pb-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Studi Kasus</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Penanganan yang Telah Kami Selesaikan' }}</h2>
            </div>
            <p class="text-slate-600 lg:col-span-5">{{ $section->subtitle ?: 'Ringkasan sebagian penugasan yang dapat kami bagikan, dengan tetap menjaga kerahasiaan klien.' }}</p>
        </div>

        <div class="divide-y divide-slate-200">
            @foreach ($company->projects as $project)
                <article class="group grid gap-6 py-10 md:grid-cols-12 md:gap-8">
                    <div class="md:col-span-3">
                        <div class="overflow-hidden rounded-brand">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" icon="document" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" />
                        </div>
                    </div>
                    <div class="md:col-span-6">
                        <div class="flex flex-wrap items-center gap-3 text-xs">
                            <span class="font-heading text-secondary">Kasus {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @if ($project->category)
                                <span class="rounded-full bg-primary/10 px-3 py-1 font-semibold text-primary">{{ $project->category }}</span>
                            @endif
                        </div>
                        <h3 class="mt-3 font-heading text-2xl leading-snug text-slate-900">
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $project->title }} <x-icon name="arrow-up-right" class="inline size-5 align-baseline opacity-50" /></a>
                            @else
                                {{ $project->title }}
                            @endif
                        </h3>
                        <p class="mt-3 leading-relaxed text-slate-600">{{ $project->description }}</p>
                    </div>
                    <dl class="grid grid-cols-3 gap-4 self-start rounded-brand bg-slate-50 p-5 text-sm md:col-span-3 md:grid-cols-1">
                        <div>
                            <dt class="text-[11px] font-semibold tracking-wide text-slate-500 uppercase">Klien</dt>
                            <dd class="mt-0.5 font-medium text-slate-900">{{ $project->client ?: 'Rahasia' }}</dd>
                        </div>
                        @if ($project->location)
                            <div>
                                <dt class="text-[11px] font-semibold tracking-wide text-slate-500 uppercase">Lokasi</dt>
                                <dd class="mt-0.5 font-medium text-slate-900">{{ $project->location }}</dd>
                            </div>
                        @endif
                        @if ($project->year)
                            <div>
                                <dt class="text-[11px] font-semibold tracking-wide text-slate-500 uppercase">Tahun</dt>
                                <dd class="mt-0.5 font-medium text-slate-900">{{ $project->year }}</dd>
                            </div>
                        @endif
                    </dl>
                </article>
            @endforeach
        </div>
    </div>
</section>
