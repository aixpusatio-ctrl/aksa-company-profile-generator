{{-- Projects: Alternating — large image / text rows alternating left and right. --}}
@php($projects = $company->projects)
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Proyek yang Kami Kerjakan', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-16 space-y-20 lg:mt-24 lg:space-y-28">
            @foreach ($projects as $project)
                @php($flip = $loop->even)
                <article class="group grid items-center gap-8 lg:grid-cols-12 lg:gap-14">
                    <div class="lg:col-span-7 {{ $flip ? 'lg:order-2' : '' }}" {!! $ds->reveal(0, $flip ? 'right' : 'left') !!}>
                        <div class="relative overflow-hidden {{ $ds->img() }}">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover transition duration-[1400ms] group-hover:scale-105 lg:aspect-[7/5]" />
                            @if ($project->year)<span class="absolute bottom-4 {{ $flip ? 'left-4' : 'right-4' }} rounded-full bg-black/55 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $project->year }}</span>@endif
                        </div>
                    </div>
                    <div class="lg:col-span-5 {{ $flip ? 'lg:order-1' : '' }}" {!! $ds->reveal(1) !!}>
                        <span class="heading block text-5xl text-primary/30 sm:text-6xl" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        @if ($project->category)<p class="mt-4 text-xs font-semibold tracking-[0.2em] text-primary uppercase">{{ $project->category }}</p>@endif
                        <h3 class="heading mt-2 text-3xl break-words sm:text-4xl">{{ $project->title }}</h3>
                        @if ($project->description)<p class="mt-5 leading-relaxed text-muted">{{ $project->description }}</p>@endif
                        @if ($project->client || $project->location)
                            <ul class="mt-6 space-y-2 border-t border-line pt-5 text-sm text-muted">
                                @if ($project->client)<li class="flex items-center gap-2.5"><x-icon name="building" class="size-4 shrink-0 text-primary" /> <span class="break-words">{{ $project->client }}</span></li>@endif
                                @if ($project->location)<li class="flex items-center gap-2.5"><x-icon name="map-pin" class="size-4 shrink-0 text-primary" /> <span class="break-words">{{ $project->location }}</span></li>@endif
                            </ul>
                        @endif
                        @if ($project->url)
                            <div class="mt-7">@include('components.company.partials.button', ['href' => $project->url, 'label' => 'Lihat proyek', 'kind' => 'link', 'external' => true])</div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
