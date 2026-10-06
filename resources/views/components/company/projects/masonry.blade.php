{{-- Projects: Masonry — CSS-columns masonry with varied aspect ratios and an info overlay on hover. --}}
@php($ratios = ['aspect-[4/5]', 'aspect-[4/3]', 'aspect-square', 'aspect-[3/4]', 'aspect-[16/11]', 'aspect-[5/6]'])
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Karya Pilihan', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 gap-5 sm:columns-2 lg:columns-3 [&>*]:mb-5">
            @foreach ($company->projects as $project)
                @php($tag = $project->url ? 'a' : 'div')
                <{{ $tag }} @if ($project->url) href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" @endif
                    class="group relative block break-inside-avoid overflow-hidden {{ $ds->img() }}" {!! $ds->reveal($loop->index % 3) !!}>
                    <x-site.img :src="$project->url('image')" :alt="$project->title" class="{{ $ratios[$loop->index % count($ratios)] }} w-full object-cover transition duration-[1200ms] group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition duration-500 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-visible:opacity-100"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 text-white transition duration-500 sm:p-6 lg:translate-y-4 lg:opacity-0 lg:group-hover:translate-y-0 lg:group-hover:opacity-100 lg:group-focus-visible:translate-y-0 lg:group-focus-visible:opacity-100">
                        <p class="flex flex-wrap items-center gap-x-2 text-[11px] font-semibold tracking-[0.16em] text-white/75 uppercase">
                            @if ($project->category)<span>{{ $project->category }}</span>@endif
                            @if ($project->category && $project->year)<span aria-hidden="true">&middot;</span>@endif
                            @if ($project->year)<span>{{ $project->year }}</span>@endif
                        </p>
                        <h3 class="heading mt-1.5 text-xl leading-tight break-words sm:text-2xl">{{ $project->title }}</h3>
                        @if ($project->client || $project->location)
                            <p class="mt-2 text-sm text-white/80">{{ collect([$project->client, $project->location])->filter()->join(' — ') }}</p>
                        @endif
                    </div>
                    @if ($project->url)
                        <span class="absolute top-4 right-4 inline-flex size-10 items-center justify-center rounded-full bg-black/45 text-white backdrop-blur transition group-hover:rotate-45 lg:opacity-0 lg:group-hover:opacity-100"><x-icon name="arrow-up-right" class="size-4" /></span>
                    @endif
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
</section>
