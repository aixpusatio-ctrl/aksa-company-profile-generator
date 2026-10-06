{{-- Projects: Fullwidth — cinematic stacked full-bleed image slides with text overlay (stacking on scroll on desktop). --}}
@php($projects = $company->projects)
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Karya Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <p class="shrink-0 font-mono text-xs tracking-wide text-muted" {!! $ds->reveal(1) !!}>{{ str_pad($projects->count(), 2, '0', STR_PAD_LEFT) }} proyek pilihan</p>
        </div>
    </div>

    <div class="mt-14">
        @foreach ($projects as $project)
            <article class="group relative isolate flex min-h-[78svh] items-end overflow-hidden lg:sticky lg:top-0 lg:min-h-[100svh]">
                <x-site.img :src="$project->url('image')" :alt="$project->title" class="absolute inset-0 -z-20 size-full object-cover transition duration-[2000ms] group-hover:scale-[1.03]" />
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/85 via-black/35 to-black/10"></div>
                <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/45 to-transparent"></div>

                <div class="{{ $ds->container('wide') }} pt-28 pb-12 text-white sm:pb-16 lg:pb-20">
                    <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-8" {!! $ds->reveal(0) !!}>
                            <p class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold tracking-[0.2em] text-white/75 uppercase">
                                <span class="font-mono text-white">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($projects->count(), 2, '0', STR_PAD_LEFT) }}</span>
                                @if ($project->category)<span class="h-px w-8 bg-white/40"></span><span>{{ $project->category }}</span>@endif
                                @if ($project->year)<span class="h-px w-8 bg-white/40"></span><span>{{ $project->year }}</span>@endif
                            </p>
                            <h3 class="heading mt-5 text-[clamp(2.25rem,6vw,5.5rem)] leading-[0.98] break-words">{{ $project->title }}</h3>
                        </div>
                        <div class="lg:col-span-4" {!! $ds->reveal(1) !!}>
                            @if ($project->description)<p class="line-clamp-4 text-base leading-relaxed text-white/85">{{ $project->description }}</p>@endif
                            @if ($project->client || $project->location)
                                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-white/20 pt-5 text-sm">
                                    @if ($project->client)<div><dt class="text-[11px] tracking-[0.14em] text-white/60 uppercase">Klien</dt><dd class="mt-0.5 font-medium break-words">{{ $project->client }}</dd></div>@endif
                                    @if ($project->location)<div><dt class="text-[11px] tracking-[0.14em] text-white/60 uppercase">Lokasi</dt><dd class="mt-0.5 font-medium break-words">{{ $project->location }}</dd></div>@endif
                                </dl>
                            @endif
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="mt-7 inline-flex min-h-11 items-center gap-2 border-b border-white/50 pb-1 text-sm font-semibold text-white transition hover:gap-3 hover:border-white">Lihat proyek <x-icon name="arrow-up-right" class="size-4" /></a>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
