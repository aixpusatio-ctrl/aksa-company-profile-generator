{{-- Modern Business projects: featured case study card + soft cards grid. --}}
@php
    $featured = $company->projects->first();
    $others = $company->projects->slice(1);
@endphp
<section id="projects" class="py-20 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="rocket" class="size-3.5" /> Studi Kasus</span>
                <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Hasil Nyata untuk Klien Kami' }}</h2>
                @if ($section->subtitle)<p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>@endif
            </div>
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 self-start rounded-btn bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 md:self-auto">Diskusikan proyek Anda <x-icon name="arrow-right" class="size-4" /></a>
        </div>

        @if ($featured)
            <article class="group mt-14 grid overflow-hidden rounded-brand bg-slate-900 text-white shadow-2xl shadow-slate-900/20 lg:grid-cols-5">
                <div class="relative overflow-hidden lg:col-span-3">
                    <x-site.img :src="$featured->url('image')" :alt="$featured->title" class="aspect-[16/10] h-full w-full object-cover transition duration-700 group-hover:scale-105" />
                    <span class="absolute top-5 left-5 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 backdrop-blur">Unggulan</span>
                </div>
                <div class="relative flex flex-col justify-center p-8 lg:col-span-2 lg:p-12">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_100%_0%,color-mix(in_oklab,var(--brand-primary)_45%,transparent),transparent_60%)]"></div>
                    <div class="relative">
                        @if ($featured->category)<span class="rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/80 ring-1 ring-white/15">{{ $featured->category }}</span>@endif
                        <h3 class="mt-5 font-heading text-2xl font-semibold sm:text-3xl">{{ $featured->title }}</h3>
                        <p class="mt-4 text-sm leading-relaxed text-white/70">{{ $featured->description }}</p>
                        <dl class="mt-8 grid grid-cols-2 gap-4 border-t border-white/10 pt-6 text-sm">
                            @if ($featured->client)<div><dt class="text-xs text-white/50">Klien</dt><dd class="mt-1 font-medium">{{ $featured->client }}</dd></div>@endif
                            @if ($featured->year)<div><dt class="text-xs text-white/50">Tahun</dt><dd class="mt-1 font-medium">{{ $featured->year }}</dd></div>@endif
                            @if ($featured->location)<div class="col-span-2"><dt class="text-xs text-white/50">Lokasi</dt><dd class="mt-1 font-medium">{{ $featured->location }}</dd></div>@endif
                        </dl>
                        @if ($featured->url)
                            <a href="{{ $featured->url }}" target="_blank" rel="noopener noreferrer" class="mt-8 inline-flex items-center gap-2 rounded-btn bg-white px-5 py-2.5 text-sm font-semibold text-slate-900">Lihat proyek <x-icon name="arrow-up-right" class="size-4" /></a>
                        @endif
                    </div>
                </div>
            </article>
        @endif

        @if ($others->isNotEmpty())
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($others as $project)
                    <article class="group flex flex-col overflow-hidden rounded-brand bg-white shadow-[0_8px_30px_rgb(15_23_42/0.06)] ring-1 ring-slate-100 transition duration-300 hover:-translate-y-1.5 hover:shadow-xl">
                        <div class="relative m-2 overflow-hidden rounded-[calc(var(--brand-radius)-4px)]">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[16/11] w-full object-cover transition duration-700 group-hover:scale-105" />
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="absolute top-3 right-3 inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-900 shadow-lg transition group-hover:rotate-45" aria-label="Kunjungi {{ $project->title }}"><x-icon name="arrow-up-right" class="size-4" /></a>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col px-6 pt-4 pb-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                @if ($project->category)<span class="rounded-full bg-primary/10 px-2.5 py-1 font-semibold text-primary">{{ $project->category }}</span>@endif
                                @if ($project->year)<span class="rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-500">{{ $project->year }}</span>@endif
                            </div>
                            <h3 class="mt-4 font-heading text-lg font-semibold text-slate-900">{{ $project->title }}</h3>
                            <p class="mt-2 line-clamp-2 flex-1 text-sm text-slate-500">{{ $project->description }}</p>
                            @if ($project->client || $project->location)
                                <p class="mt-5 flex items-center gap-2 text-xs text-slate-400"><x-icon name="map-pin" class="size-3.5" /> {{ collect([$project->client, $project->location])->filter()->implode(' · ') }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
