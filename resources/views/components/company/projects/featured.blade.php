{{-- Projects: Featured — first project as a large case block (image + case info), remaining projects in a 3-column grid. --}}
@php
    $projects = $company->projects;
    $lead = $projects->first();
    $rest = $projects->slice(1);
@endphp
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Proyek Unggulan', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="shrink-0" {!! $ds->reveal(1) !!}>@include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Diskusikan proyek Anda', 'kind' => 'link'])</div>
        </div>

        @if ($lead)
            <article class="group mt-14 grid overflow-hidden lg:grid-cols-12 {{ $ds->card('', false) }}" {!! $ds->reveal(0) !!}>
                <div class="relative overflow-hidden lg:col-span-7">
                    <x-site.img :src="$lead->url('image')" :alt="$lead->title" class="aspect-[16/10] h-full w-full object-cover transition duration-[1200ms] group-hover:scale-[1.03] lg:aspect-auto lg:min-h-[30rem]" />
                    <span class="absolute top-5 left-5 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold tracking-wide text-white backdrop-blur">Proyek unggulan</span>
                </div>
                <div class="flex flex-col p-7 sm:p-10 lg:col-span-5 lg:p-12">
                    @if ($lead->category)<p class="text-xs font-semibold tracking-[0.18em] text-primary uppercase">{{ $lead->category }}</p>@endif
                    <h3 class="heading mt-3 text-3xl break-words sm:text-4xl">{{ $lead->title }}</h3>
                    @if ($lead->description)<p class="mt-5 text-muted leading-relaxed">{{ $lead->description }}</p>@endif
                    <dl class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-brand border border-line bg-line text-sm">
                        @foreach (['Klien' => $lead->client, 'Lokasi' => $lead->location, 'Tahun' => $lead->year, 'Kategori' => $lead->category] as $label => $value)
                            @if (filled($value))
                                <div class="bg-card px-4 py-3">
                                    <dt class="text-[11px] tracking-[0.14em] text-muted uppercase">{{ $label }}</dt>
                                    <dd class="mt-0.5 font-medium break-words text-ink">{{ $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                    @if ($lead->url)
                        <div class="mt-auto pt-8">@include('components.company.partials.button', ['href' => $lead->url, 'label' => 'Lihat proyek', 'kind' => 'primary', 'external' => true, 'class' => 'w-full sm:w-auto'])</div>
                    @endif
                </div>
            </article>
        @endif

        @if ($rest->isNotEmpty())
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($rest as $project)
                    <article class="{{ $ds->card('group overflow-hidden') }}" {!! $ds->reveal($loop->index) !!}>
                        <div class="relative overflow-hidden">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" />
                            @if ($project->year)<span class="absolute top-4 left-4 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $project->year }}</span>@endif
                        </div>
                        <div class="p-6">
                            @if ($project->category)<p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $project->category }}</p>@endif
                            <h3 class="heading mt-1 text-lg break-words">
                                @if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $project->title }}</a>@else{{ $project->title }}@endif
                            </h3>
                            @if ($project->description)<p class="mt-2 line-clamp-2 text-sm text-muted">{{ $project->description }}</p>@endif
                            @if ($project->client || $project->location)
                                <p class="mt-4 flex flex-wrap gap-x-4 gap-y-1 border-t border-line pt-4 text-xs text-muted">
                                    @if ($project->client)<span class="inline-flex items-center gap-1"><x-icon name="building" class="size-3.5" /> {{ $project->client }}</span>@endif
                                    @if ($project->location)<span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="size-3.5" /> {{ $project->location }}</span>@endif
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
